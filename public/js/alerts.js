$(document).ready(function(){
    function checkLowStock(){
        $.ajax({
            url: '../api/low_stock.php',
            method: 'GET',
            success: function(res){
                let data = JSON.parse(res);
                if(data.status === 'success' && data.items.length > 0){
                    let html = '<ul>';
                    data.items.forEach(item => {
                        html += `<li>${item.name} - Stock: ${item.stock}</li>`;
                    });
                    html += '</ul>';

                    $('#lowStockAlert').html(html).show();
                } else {
                    $('#lowStockAlert').hide();
                }
            }
        });
    }

    // Check immediately and every 5 minutes
    checkLowStock();
    setInterval(checkLowStock, 5 * 60 * 1000);
});
