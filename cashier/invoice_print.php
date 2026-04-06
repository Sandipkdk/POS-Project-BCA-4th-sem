<?php
$required_roles = ['admin', 'cashier'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';

// Validate
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("Invalid invoice ID");
}

$id = intval($_GET['id']);

// Fetch sale
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

if (!$sale) exit("Sale not found");

// Fetch items
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice_<?= htmlspecialchars($sale['invoice_no']) ?></title>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            color: #000;
            line-height: 1.4;
        }

        .invoice-container {
            max-width: 700px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 30px;
            border: none;
        }

        .business-logo {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
        }

        .text-end { text-align: right; }
        .text-center { text-align: center; }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            font-size: 14px;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.items-table th {
            border-bottom: 2px solid #333;
            padding: 10px 5px;
            text-align: left;
            font-size: 13px;
        }

        table.items-table td {
            padding: 10px 5px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .totals-wrapper {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        .totals-table {
            width: 250px;
        }

        .totals-table td {
            padding: 5px 0;
            border: none;
        }

        .grand-total {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #333 !important;
            padding-top: 10px !important;
        }

        .footer-note {
            text-align: center;
            margin-top: 50px;
            font-size: 12px;
            color: #777;
            border-top: 1px dashed #ccc;
            padding-top: 20px;
        }

        .refunded-banner {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        @media print {
            .no-print { display: none; }
            .invoice-container { border: none; width: 100%; padding: 0; }
            body { padding: 0; }
        }
    </style>
</head>

<body onload="window.print()">

<div class="invoice-container">
    <?php if ($sale['is_refunded']): ?>
        <div class="refunded-banner">
            THIS INVOICE HAS BEEN REFUNDED
        </div>
    <?php endif; ?>

    <table class="header-table">
        <tr>
            <td>
                <div class="business-logo text-uppercase">General Store</div>
                <div style="font-size: 13px; color: #555;">
                    Kathmandu-1, Nepal<br>
                    Phone: +977 98XXXXXXXX<br>
                    PAN/VAT No: 60XXXXXXXX
                </div>
            </td>
            <td class="text-end">
                <h2 style="margin:0; color: #2c3e50;">INVOICE</h2>
                <p style="margin:5px 0;">#<?= htmlspecialchars($sale['invoice_no']) ?></p>
                <p style="font-size:12px; color:#666;"><?= date('d M Y, h:i A', strtotime($sale['created_at'])) ?></p>
            </td>
        </tr>
    </table>

    <div class="info-section">
        <div>
            <strong>Customer Details:</strong><br>
            <?= htmlspecialchars($sale['customer_name'] ?? "Walk-in Customer") ?><br>
            Payment: <?= ucfirst($sale['payment_method']) ?>
        </div>
        <div class="text-end">
            <strong>Transaction Info:</strong><br>
            Bill ID: #<?= $sale['bill_id'] ?><br>
            Cashier: <?= htmlspecialchars($sale['cashier_name']) ?>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-end">Rate</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Disc%</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
        <?php while($item = $items->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td class="text-end">Rs. <?= number_format($item['price'], 2) ?></td>
                <td class="text-center"><?= $item['qty'] ?></td>
                <td class="text-end"><?= $item['discount'] ?>%</td>
                <td class="text-end">Rs. <?= number_format($item['total'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <div class="totals-wrapper">
        <table class="totals-table">
            <tr>
                <td>Discount:</td>
                <td class="text-end">Rs. <?= number_format($sale['bill_discount'], 2) ?></td>
            </tr>
            <tr>
                <td>Tax:</td>
                <td class="text-end">Rs. <?= number_format($sale['bill_tax'], 2) ?></td>
            </tr>
            <tr class="grand-total">
                <td>Grand Total:</td>
                <td class="text-end">Rs. <?= number_format($sale['total'], 2) ?></td>
            </tr>
        </table>
    </div>

    <div class="footer-note">
        <p>Thank you for shopping with us!</p>
        <p>Software Generated Invoice</p>
    </div>

    <div class="no-print text-center" style="margin-top: 30px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 4px;">
            Re-print Invoice
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 4px; margin-left: 10px;">
            Close Window
        </button>
    </div>
</div>

</body>
</html>