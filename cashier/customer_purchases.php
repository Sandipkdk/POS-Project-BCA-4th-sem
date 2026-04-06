<?php
$required_roles = ['admin','cashier'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

$customer_id = intval($_GET['id'] ?? 0);
if(!$customer_id) exit('Invalid customer ID');

// Fetch customer info
$customer = $conn->query("SELECT * FROM customers WHERE id=$customer_id")->fetch_assoc();
if(!$customer) exit('Customer not found');

// Fetch sales
$sales = $conn->query("SELECT * FROM sales WHERE customer_id=$customer_id ORDER BY created_at DESC");
?>

<div class="container-fluid">
    <h2>Purchases of <?= htmlspecialchars($customer['name']) ?></h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Bill ID</th>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Total</th>
                <th>Payment Method</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if($sales->num_rows > 0): ?>
            <?php while($sale = $sales->fetch_assoc()): ?>
                <tr>
                    <td><?= $sale['bill_id'] ?></td>
                    <td><?= $sale['invoice_no'] ?></td>
                    <td><?= date('d M Y H:i', strtotime($sale['created_at'])) ?></td>
                    <td><?= number_format($sale['total'], 2) ?></td>
                    <td><?= $sale['payment_method'] ?></td>
                    <td><a href="../cashier/invoice_view.php?id=<?= $sale['id'] ?>" class="btn btn-sm btn-primary">View</a></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No purchases found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
