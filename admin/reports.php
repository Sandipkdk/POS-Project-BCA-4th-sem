<?php
// Both cashier and admin can access
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

// Filter by date if provided
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$where = '';
if($start_date) $where .= " AND DATE(s.created_at) >= '$start_date'";
if($end_date) $where .= " AND DATE(s.created_at) <= '$end_date'";

// Fetch sales
$sales = $conn->query("SELECT s.*, c.name AS customer_name 
                       FROM sales s 
                       LEFT JOIN customers c ON s.customer_id=c.id
                       WHERE 1 $where
                       ORDER BY s.created_at DESC");

// Total sales and count
$total_sales = 0;
$total_count = 0;
while($row = $sales->fetch_assoc()){
    $total_sales += $row['total'];
    $total_count++;
}
$sales->data_seek(0); // reset pointer
?>

<div class="container-fluid">
    <h2>Sales Report</h2>

    <form method="GET" class="mb-3 row g-3">
        <div class="col-md-3">
            <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
        </div>
        <div class="col-md-3">
            <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <div class="mb-3">
        <strong>Total Sales:</strong> $<?= number_format($total_sales,2) ?> | 
        <strong>Total Invoices:</strong> <?= $total_count ?>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Bill ID</th>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Payment Method</th>
            </tr>
        </thead>
        <tbody>
            <?php if($sales->num_rows > 0): ?>
                <?php while($row = $sales->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['bill_id'] ?></td>
                        <td><?= $row['invoice_no'] ?></td>
                        <td><?= $row['customer_name'] ?></td>
                        <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                        <td><?= number_format($row['total'],2) ?></td>
                        <td><?= $row['payment_method'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No sales found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
