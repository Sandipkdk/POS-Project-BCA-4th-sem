<?php
// Note: Usually, admins or senior cashiers handle refunds. 
// If you want only admins, change this to ['admin'].
$required_roles = ['admin', 'cashier']; 
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

// Search Logic
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $search_query = " AND (s.bill_id LIKE '%$search%' OR s.invoice_no LIKE '%$search%' OR c.name LIKE '%$search%') ";
}

// Fetch sales that are NOT yet refunded
$query = "SELECT s.*, c.name AS customer_name 
          FROM sales s
          LEFT JOIN customers c ON s.customer_id = c.id
          WHERE s.is_refunded = 0 $search_query
          ORDER BY s.created_at DESC LIMIT 50";
$sales = $conn->query($query);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-danger"><i class="bi bi-arrow-counterclockwise me-2"></i>Refunds & Returns</h2>
        
        <form method="GET" class="d-flex" style="max-width: 300px;">
            <div class="input-group shadow-sm">
                <input type="text" name="search" class="form-control" placeholder="Search Bill ID/Name..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button class="btn btn-dark" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-list-check me-2"></i>Eligible Sales for Refund</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Bill ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Payment</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($sales && $sales->num_rows > 0): ?>
                        <?php while($row = $sales->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-3 fw-bold">#<?= $row['bill_id'] ?></td>
                                <td><?= htmlspecialchars($row['customer_name'] ?? 'Walk-in') ?></td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?><br>
                                        <?= date('h:i A', strtotime($row['created_at'])) ?>
                                    </small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= ucfirst($row['payment_method']) ?></span></td>
                                <td class="text-end fw-bold">Rs. <?= number_format($row['total'], 2) ?></td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm">
                                        <a href="invoice_view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button onclick="refundSale(<?= $row['id'] ?>, '<?= $row['bill_id'] ?>', <?= $row['total'] ?>)" 
                                                class="btn btn-sm btn-danger">
                                            <i class="bi bi-arrow-left-right me-1"></i> Refund
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block"></i> No refundable sales found.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Triggers the refund process
 * @param {number} id - Internal database ID
 * @param {string} billId - The displayable Bill ID
 * @param {number} amount - The amount to be refunded
 */
function refundSale(id, billId, amount){
    const formattedAmount = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'NPR' }).format(amount);
    
    // Safety check: Refunds are sensitive
    if(!confirm(`⚠️ REFUND WARNING\n\nAre you sure you want to refund Bill #${billId}?\nAmount: ${formattedAmount}\n\nThis will mark the invoice as refunded and items should be physically returned to stock.`)) {
        return;
    }

    // Visual feedback (disable button)
    const btn = event.currentTarget;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

    $.ajax({
        url: '../api/process_refund.php',
        method: 'POST',
        data: { sale_id: id },
        dataType: 'json',
        success: function(resp){
            if(resp.status === 'success' || resp.status === true){
                alert('✅ Refund successful! Stock has been updated.');
                location.reload();
            } else {
                alert('❌ Error: ' + (resp.message || 'Refund failed'));
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-arrow-left-right me-1"></i> Refund';
            }
        },
        error: function(){
            alert('❌ Network error. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-left-right me-1"></i> Refund';
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>