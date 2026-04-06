<?php
/**
 * ADMIN DASHBOARD
 * Core management view for administrators.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Security Check: Admin role only
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

/* --- DATA FETCHING --- */

// A. LOW STOCK: Products requiring restock
$threshold = 5;
$low_stock_res = $conn->query("SELECT name, stock FROM products WHERE stock <= $threshold ORDER BY stock ASC");
$low_stock_items = $low_stock_res->fetch_all(MYSQLI_ASSOC);

// B. SALES KPI: Calculation excluding refunded transactions
$today = date('Y-m-d');
$this_month = date('Y-m');

$today_sales = $conn->query("SELECT SUM(total) AS total FROM sales WHERE DATE(created_at)='$today' AND is_refunded=0")->fetch_assoc()['total'] ?? 0;
$month_sales = $conn->query("SELECT SUM(total) AS total FROM sales WHERE DATE_FORMAT(created_at,'%Y-%m')='$this_month' AND is_refunded=0")->fetch_assoc()['total'] ?? 0;
$total_sales = $conn->query("SELECT SUM(total) AS total FROM sales WHERE is_refunded=0")->fetch_assoc()['total'] ?? 0;

// C. RECENT TRANSACTIONS
$recent_sales = $conn->query("
    SELECT s.*, c.name AS customer_name
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    ORDER BY s.created_at DESC
    LIMIT 10
");

// D. PENDING REFUNDS
$pending_refunds = $conn->query("
    SELECT s.*, c.name AS customer_name
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    WHERE s.is_refunded = 0
    ORDER BY s.created_at DESC
    LIMIT 5
");
?>

<style>
    .summary-box { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #fff; }
    .summary-box strong { font-size: 22px; color: #333; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; }
    table th, table td { border: 1px solid #eee; padding: 12px; text-align: left; }
    table th { background: #f8f9fa; }
    .alert-box { border-left: 5px solid #f0ad4e; background: #fff3cd; padding: 15px; margin-bottom: 20px; }
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
    .badge-paid { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .badge-refunded { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .badge-payment { background: #e9ecef; color: #495057; border: 1px solid #dee2e6; }
    .admin-card { border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; transition: 0.3s; text-decoration: none; color: inherit; display: block; }
    .admin-card:hover { background-color: #f8f9fa; border-color: #0d6efd; }
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Admin Control Panel</h2>
        <span class="text-muted">Welcome, Admin</span>
    </div>

    <?php if (count($low_stock_items) > 0): ?>
        <div class="alert-box shadow-sm rounded">
            <strong><i class="bi bi-shield-exclamation"></i> Critical Stock Levels:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($low_stock_items as $item): ?>
                    <li><?= htmlspecialchars($item['name']) ?> (Remaining: <?= $item['stock'] ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row text-center mb-4">
        <div class="col-md-4">
            <div class="summary-box shadow-sm">
                <small class="text-uppercase text-muted">Daily Revenue</small><br>
                <strong>Rs. <?= number_format($today_sales, 2) ?></strong>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box shadow-sm">
                <small class="text-uppercase text-muted">Monthly Revenue</small><br>
                <strong>Rs. <?= number_format($month_sales, 2) ?></strong>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box shadow-sm">
                <small class="text-uppercase text-muted">Total Gross</small><br>
                <strong>Rs. <?= number_format($total_sales, 2) ?></strong>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-end mb-2">
            <h4 class="mb-0">Recent Transactions</h4>
            <a href="../cashier/invoices.php" class="btn btn-sm btn-outline-primary">View All Invoices</a>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Bill ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($sale = $recent_sales->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $sale['bill_id'] ?></td>
                            <td><?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in') ?></td>
                            <td>Rs. <?= number_format($sale['total'], 2) ?></td>
                            <td><span class="badge badge-payment"><?= strtoupper($sale['payment_method']) ?></span></td>
                            <td>
                                <?php if($sale['is_refunded']): ?>
                                    <span class="badge badge-refunded">Refunded</span>
                                <?php else: ?>
                                    <span class="badge badge-paid">Paid</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d M Y', strtotime($sale['created_at'])) ?></td>
                            <td><a href="../cashier/invoice_view.php?id=<?= $sale['id'] ?>" class="btn btn-sm btn-light border">View</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Quick Refund Processing</h4>
            <a href="../cashier/refunds.php" class="btn btn-outline-danger btn-sm">Refund Manager &rarr;</a>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Bill ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pending_refunds->num_rows > 0): ?>
                        <?php while ($refund = $pending_refunds->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $refund['bill_id'] ?></td>
                                <td><?= htmlspecialchars($refund['customer_name'] ?? 'Walk-in') ?></td>
                                <td>Rs. <?= number_format($refund['total'], 2) ?></td>
                                <td><?= strtoupper($refund['payment_method']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger px-3" onclick="refundSale(<?= $refund['id'] ?>)">Process Refund</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-3">No pending transactions for refund.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- <h4 class="mb-3">Administrative Tools</h4>
    <div class="row">
        <div class="col-md-6 mb-3">
            <a href="reports.php" class="admin-card text-center">
                <i class="bi bi-graph-up-arrow fs-2 text-primary"></i>
                <h5 class="mt-2">Reports & Analytics</h5>
                <p class="text-muted small mb-0">View sales performance, item-wise analysis, and tax reports.</p>
            </a>
        </div>
        <div class="col-md-6 mb-3">
            <a href="setting.php" class="admin-card text-center">
                <i class="bi bi-gear-wide-connected fs-2 text-secondary"></i>
                <h5 class="mt-2">System Settings</h5>
                <p class="text-muted small mb-0">Configure store info, tax rates, and user permissions.</p>
            </a>
        </div>
    </div> -->

</div>

<script>
/**
 * AJAX REFUND HANDLER
 */
function refundSale(id) {
    if (!confirm('Refund this sale? This will update stock and mark the invoice as refunded.')) return;

    fetch('../cashier/api/process_refund.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'sale_id=' + id
    })
    .then(res => res.json())
    .then(data => {
        if(data.status == 1) {
            alert('✅ Success: ' + data.message);
            location.reload();
        } else {
            alert('SUcess ' + data.message);
        }
    })
    .catch(err => alert('Network error. Check connection.'));
}
</script>

<?php include '../includes/footer.php'; ?>