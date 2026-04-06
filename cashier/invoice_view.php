<?php
$required_roles = ['admin', 'cashier'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

// Validate invoice ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid invoice ID'); window.location='invoices.php';</script>";
    exit();
}

$id = intval($_GET['id']);

// Fetch sale (Prepared Statement)
$stmt = $conn->prepare("
    SELECT s.*, 
           c.name AS customer_name, 
           u.name AS cashier_name,
           (SELECT name FROM users WHERE id = s.refunded_by) AS refunded_by_name
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    LEFT JOIN users u ON s.created_by = u.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$sale = $stmt->get_result()->fetch_assoc();

if (!$sale) {
    echo "<script>alert('Invoice not found'); window.location='invoices.php';</script>";
    exit();
}

// Fetch sale items
$stmt_items = $conn->prepare("
    SELECT si.*, p.name AS product_name
    FROM sales_items si
    LEFT JOIN products p ON si.product_id = p.id
    WHERE si.sale_id = ?
");
$stmt_items->bind_param("i", $id);
$stmt_items->execute();
$items = $stmt_items->get_result();
?>

<style>
.invoice-card { max-width: 900px; margin: 20px auto; }
.invoice-header { border-bottom: 2px solid #f4f4f4; padding-bottom: 20px; }
.table th { background-color: #f8f9fa; text-transform: uppercase; font-size: 0.85rem; }
.summary-label { font-weight: 600; color: #666; }
.total-row { background-color: #f0f7ff; border-top: 2px solid #0d6efd !important; }
@media print {
    .btn, .sidebar, .header { display: none !important; }
    .invoice-card { width: 100%; max-width: 100%; margin: 0; border: none; }
}
</style>

<div class="container-fluid">
    <div class="card shadow-sm invoice-card border-0">
        <div class="card-body p-5">

            <div class="invoice-header d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h2 class="text-primary fw-bold mb-1">INVOICE</h2>
                    <p class="text-muted mb-0">No: <strong><?= htmlspecialchars($sale['invoice_no']) ?></strong></p>
                    <p class="text-muted small">Bill ID: #<?= $sale['bill_id'] ?></p>
                </div>
                <div class="text-end">
                    <?php if ($sale['is_refunded']): ?>
                        <span class="badge bg-danger fs-6 mb-2">REFUNDED</span>
                    <?php else: ?>
                        <span class="badge bg-success fs-6 mb-2">PAID</span>
                    <?php endif; ?>
                    <p class="mb-0 fw-bold"><?= date('d M Y, h:i A', strtotime($sale['created_at'])) ?></p>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-sm-6">
                    <h6 class="summary-label text-uppercase small">Billed To:</h6>
                    <p class="fs-5 fw-bold mb-1"><?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer') ?></p>
                    <p class="text-muted small">Payment Method: <span class="badge bg-light text-dark border"><?= ucfirst($sale['payment_method']) ?></span></p>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <h6 class="summary-label text-uppercase small">Issued By:</h6>
                    <p class="mb-1 fw-bold"><?= htmlspecialchars($sale['cashier_name']) ?></p>
                    <p class="text-muted small">Store POS System</p>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-borderless border-bottom">
                    <thead>
                        <tr class="border-bottom">
                            <th>Product Description</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Disc. %</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $items->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td class="text-end">Rs. <?= number_format($item['price'], 2) ?></td>
                                <td class="text-center"><?= $item['qty'] ?></td>
                                <td class="text-center text-muted"><?= $item['discount'] ?>%</td>
                                <td class="text-end fw-bold">Rs. <?= number_format($item['total'], 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-5">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="summary-label">Bill Discount:</td>
                            <td class="text-end text-danger">- Rs. <?= number_format($sale['bill_discount'], 2) ?></td>
                        </tr>
                        <tr>
                            <td class="summary-label">Tax Applied:</td>
                            <td class="text-end">+ Rs. <?= number_format($sale['bill_tax'], 2) ?></td>
                        </tr>
                        <tr class="total-row">
                            <td class="fs-5 fw-bold py-3">Grand Total:</td>
                            <td class="fs-5 fw-bold py-3 text-end text-primary">Rs. <?= number_format($sale['total'], 2) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php if ($sale['is_refunded']): ?>
                <div class="alert alert-warning border-0 shadow-sm mt-4 d-flex align-items-center">
                    <i class="bi bi-info-circle-fill fs-3 me-3"></i>
                    <div>
                        <div class="fw-bold">Refund Processed</div>
                        <small>Refunded by <?= htmlspecialchars($sale['refunded_by_name']) ?> on <?= date('d M Y', strtotime($sale['refunded_at'])) ?></small>
                    </div>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                <a href="invoices.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
                <div>
                    <button onclick="window.print()" class="btn btn-primary me-2">
                        <i class="bi bi-printer me-1"></i> Quick Print
                    </button>
                    <a href="invoice_print.php?id=<?= $sale['id'] ?>" target="_blank" class="btn btn-success">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Formal Invoice
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>