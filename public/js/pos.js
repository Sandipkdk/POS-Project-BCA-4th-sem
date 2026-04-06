let currentCart = {}; // holds latest cart from server

$(function(){

    /* ---------------- PRODUCT SEARCH ---------------- */
    $('#product_search').on('input', function(){
        let q = $(this).val();
        if(q.length < 2){ $('#search_results').html(''); return; }
        $.post('../api/search_product.php', {query:q}, function(res){
            $('#search_results').html(res);
        });
    });

    /* ---------------- ADD TO CART ---------------- */
    $(document).on('click', '.add-to-cart', function(){
        let id = $(this).data('id');
        $.post('../api/add_to_cart.php', {product_id:id}, updateCart, 'json');
    });

    /* ---------------- UPDATE QTY ---------------- */
    // CHANGED: Use 'change' instead of 'input' so it doesn't interrupt typing
    $(document).on('change', '.qty', function(){
        let row = $(this).closest('tr');
        let id = row.data('id');
        let qty = parseInt($(this).val());
        
        // Validation logic
        let availableStock = currentCart[id] ? parseInt(currentCart[id].stock) : 0;

        if (qty > availableStock) {
            alert(`Error: Only ${availableStock} units available in stock.`);
            $(this).val(availableStock);
            qty = availableStock;
        }

        if (qty < 1 || isNaN(qty)) {
            $(this).val(1);
            qty = 1;
        }

        $.post('../api/update_cart.php', {product_id:id, qty:qty}, updateCart, 'json');
    });

    /* ---------------- REMOVE FROM CART ---------------- */
    $(document).on('click', '.remove', function(){
        let id = $(this).closest('tr').data('id');
        $.post('../api/remove_from_cart.php', {product_id:id}, updateCart, 'json');
    });

    /* ---------------- UPDATE CART TABLE ---------------- */
    function updateCart(res){
        if(!res.status) return alert(res.message || 'Cart error');

        currentCart = res.cart;

        let tbody = '';
        Object.values(res.cart).forEach(i => {
            let t = i.price * i.qty;
            
            tbody += `<tr data-id="${i.id}">
                <td>
                    ${i.name}
                </td>
                <td>${parseFloat(i.price).toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control qty" 
                        value="${i.qty}" min="1" max="${i.stock}">
                </td>
                <td class="item-total">Rs ${t.toFixed(2)}</td>
                <td><button class="btn btn-sm btn-danger remove">X</button></td>
            </tr>`;
        });
        $('#cart_table tbody').html(tbody);
        recalcTotal();
    }

    /* ---------------- DISCOUNT / TAX ---------------- */
    $('#bill_discount_value, #bill_discount_type, #bill_tax').on('input change', function(){
        recalcTotal();
    });

    /* ---------------- RECALCULATE TOTAL ---------------- */
    function recalcTotal() {
        let subtotal = 0;

        $('#cart_table tbody tr').each(function() {
            let price = parseFloat($(this).find('td:nth-child(2)').text()) || 0;
            let qty = parseInt($(this).find('.qty').val()) || 0;
            subtotal += price * qty;
        });

        $('#subtotal_amount').text(subtotal.toFixed(2));

        let discount = Math.max(0, parseFloat($('#bill_discount_value').val()) || 0);
        let tax = Math.max(0, parseFloat($('#bill_tax').val()) || 0);
        
        if(parseFloat($('#bill_discount_value').val()) < 0) $('#bill_discount_value').val(0);
        if(parseFloat($('#bill_tax').val()) < 0) $('#bill_tax').val(0);

        let type = $('#bill_discount_type').val();
        let total = subtotal;

        if (type === 'percent') {
            total -= total * (Math.min(discount, 100) / 100);
        } else {
            total -= discount;
        }

        total += total * (tax / 100);

        if (total < 0) total = 0;

        $('#total_amount').text('Rs ' + total.toFixed(2));
    }

    /* ---------------- CUSTOMER CHECK ---------------- */
    $('#customer_phone').on('blur', function(){
        let phone = $(this).val().trim();
        if(!phone) return;
        $.post('../api/fetch_customer.php', {phone}, function(res){
            if(res.status){
                $('#customer_id').val(res.data.id);
                $('#customer_info').text('Customer: '+res.data.name);
            } else {
                $('#addCustomerModal input[name="phone"]').val(phone);
                $('#addCustomerModal').modal('show');
            }
        }, 'json');
    });

    /* ---------------- ADD CUSTOMER ---------------- */
    $('#add_customer_form').on('submit', function(e){
        e.preventDefault();
        $.post('../api/add_customer.php', $(this).serialize(), function(res){
            if(res.status){
                $('#customer_id').val(res.data.id);
                $('#customer_info').text('Customer: '+res.data.name);
                $('#addCustomerModal').modal('hide');
            } else alert(res.message);
        }, 'json');
    });

    /* ---------------- COMPLETE SALE ---------------- */
    $('#save_sale').on('click', function() {
        let customer_id = $('#customer_id').val();
        let cartCount = $('#cart_table tbody tr').length;

        if (cartCount === 0) {
            alert('❌ Error: Cannot complete sale with an empty cart.');
            return;
        }

        if (!customer_id) {
            alert('❌ Error: Please search for or add a customer first.');
            $('#customer_phone').focus();
            return;
        }

        let data = {
            customer_id: customer_id,
            bill_discount: $('#bill_discount_value').val(),
            bill_discount_type: $('#bill_discount_type').val(),
            bill_tax: $('#bill_tax').val(),
            payment_method: $('#payment_method').val() 
        };

        let btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

        $.post('../api/save_sale.php', data, function(res) {
            if (res.status) {
                alert('✅ Sale completed! Bill ID: ' + res.bill_id);
                location.reload();
            } else {
                alert('❌ Error: ' + res.message);
                btn.prop('disabled', false).text('Complete Sale');
            }
        }, 'json').fail(function() {
            alert('❌ Server error occurred.');
            btn.prop('disabled', false).text('Complete Sale');
        });
    });

    $.getJSON('../api/fetch_cart.php', updateCart);
});