<?php
/**
 * CASHIER DASHBOARD
 * Features: Low stock alerts, Revenue KPIs, Recent Transactions with Status/Payment, and Quick Refunds.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Security & Dependencies
$required_roles = ['admin','cashier'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

/* --- DATA FETCHING --- */

// A. LOW STOCK: Check items hitting the threshold of 5
$threshold = 5;
$low_stock_res = $conn->query("SELECT name, stock FROM products WHERE stock <= $threshold ORDER BY stock ASC");
$low_stock_items = $low_stock_res->fetch_all(MYSQLI_ASSOC);

// B. SALES KPI: Calculate totals excluding any refunded amounts
$today = date('Y-m-d');
$this_month = date('Y-m');

$today_sales = $conn->query("SELECT SUM(total) AS total FROM sales WHERE DATE(created_at)='$today' AND is_refunded=0")->fetch_assoc()['total'] ?? 0;
$month_sales = $conn->query("SELECT SUM(total) AS total FROM sales WHERE DATE_FORMAT(created_at,'%Y-%m')='$this_month' AND is_refunded=0")->fetch_assoc()['total'] ?? 0;
$total_sales = $conn->query("SELECT SUM(total) AS total FROM sales WHERE is_refunded=0")->fetch_assoc()['total'] ?? 0;

// C. RECENT TRANSACTIONS: Last 10 sales for the main table
$recent_sales = $conn->query("
    SELECT s.*, c.name AS customer_name
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    ORDER BY s.created_at DESC
    LIMIT 10
");

// D. QUICK REFUNDS: Last 5 sales that haven't been refunded yet
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
    .summary-box { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #f9f9f9; }
    .summary-box strong { font-size: 22px; color: #2c3e50; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; }
    table th, table td { border: 1px solid #eee; padding: 12px; text-align: left; }
    table th { background: #f8f9fa; font-weight: 600; }
    .alert-box { border-left: 5px solid #ffc107; background: #fff3cd; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
    .badge-paid { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .badge-refunded { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .badge-payment { background: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }
    .btn-view-all { float: right; }
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Dashboard</h2>
        <a href="pos.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> New Sale</a>
    </div>

    <?php if (count($low_stock_items) > 0): ?>
        <div class="alert-box shadow-sm">
            <strong><i class="bi bi-exclamation-circle"></i> Low Stock Alert</strong>
            <ul class="mb-0 mt-1">
                <?php foreach ($low_stock_items as $item): ?>
                    <li><?= htmlspecialchars($item['name']) ?> (<?= $item['stock'] ?> left)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row text-center">
        <div class="col-md-4">
            <div class="summary-box">
                <small class="text-muted d-block">Today</small>
                <strong>Rs. <?= number_format($today_sales, 2) ?></strong>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box">
                <small class="text-muted d-block">This Month</small>
                <strong>Rs. <?= number_format($month_sales, 2) ?></strong>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box">
                <small class="text-muted d-block">Total Revenue</small>
                <strong>Rs. <?= number_format($total_sales, 2) ?></strong>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <a href="invoices.php" class="btn btn-outline-primary btn-sm btn-view-all">View All Invoices</a>
        <h4>Recent Transactions</h4>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Bill ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date/Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($sale = $recent_sales->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold">#<?= $sale['bill_id'] ?></td>
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
                            <td><small><?= date('d M, h:i A', strtotime($sale['created_at'])) ?></small></td>
                            <td><a href="invoice_view.php?id=<?= $sale['id'] ?>" class="btn btn-sm btn-link p-0 text-decoration-none">View</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        <a href="refunds.php" class="btn btn-outline-danger btn-sm btn-view-all">Refund Manager</a>
        <h4>Quick Refund</h4>
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
                                    <button class="btn btn-sm btn-danger py-0" onclick="processRefund(<?= $refund['id'] ?>)">
                                        Refund
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-3 text-muted">No refundable sales found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
/**
 * AJAX REFUND HANDLER
 */
function processRefund(id) {
    if (!confirm('Refund this sale? This will update stock and mark the invoice as refunded.')) return;

    fetch('../api/process_refund.php', {
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
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(err => alert('Network error. Check connection.'));
}
</script>

<?php include '../includes/footer.php'; ?>