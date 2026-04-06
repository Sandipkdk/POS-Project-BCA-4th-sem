<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');
include_once '../config/db.php';

/* ---------------- 1. GET & SANITIZE POST DATA ---------------- */
$customer_id = intval($_POST['customer_id'] ?? 0);
$bill_discount = floatval($_POST['bill_discount'] ?? 0);
$bill_discount_type = $_POST['bill_discount_type'] ?? 'percent';
$bill_tax = floatval($_POST['bill_tax'] ?? 0);
$payment_method = $_POST['payment_method'] ?? 'cash';
$cash_given = floatval($_POST['cash_given'] ?? 0);

/* ---------------- 2. INITIAL VALIDATIONS ---------------- */
if (!$customer_id) {
    echo json_encode(['status' => 0, 'message' => 'Error: Please select a valid customer.']);
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo json_encode(['status' => 0, 'message' => 'Error: Cart is empty. Cannot process sale.']);
    exit;
}

/* ---------------- 3. PRE-CHECK STOCK AVAILABILITY ---------------- */
// We check EVERYTHING before we start inserting into the database
foreach ($cart as $item) {
    $p_id = intval($item['id']);
    $requested_qty = intval($item['qty']);

    $check_stmt = $conn->prepare("SELECT name, stock FROM products WHERE id = ?");
    $check_stmt->bind_param('i', $p_id);
    $check_stmt->execute();
    $res = $check_stmt->get_result()->fetch_assoc();

    if (!$res) {
        echo json_encode(['status' => 0, 'message' => "Product ID {$p_id} no longer exists."]);
        exit;
    }

    if ($res['stock'] < $requested_qty) {
        echo json_encode([
            'status' => 0, 
            'message' => "Insufficient Stock: '{$res['name']}' only has {$res['stock']} left, but you requested {$requested_qty}."
        ]);
        exit;
    }
}

/* ---------------- 4. START TRANSACTION ---------------- */
$conn->begin_transaction();

try {
    /* ---- CALCULATIONS ---- */
    $subtotal = 0;
    foreach ($cart as $item) {
        $price = floatval($item['price']);
        $qty = intval($item['qty']);
        $disc_p = floatval($item['discount'] ?? 0);

        $line = $price * $qty;
        $line -= ($line * $disc_p / 100);
        $subtotal += $line;
    }

    $discount_amount = ($bill_discount_type === 'percent') ? ($subtotal * ($bill_discount / 100)) : $bill_discount;
    $after_discount = max($subtotal - $discount_amount, 0);
    $tax_amount = $after_discount * ($bill_tax / 100);
    $total = round($after_discount + $tax_amount, 2);

    /* ---- INSERT SALE ---- */
    $bill_id = 'B' . time();
    $invoice_no = 'INV' . date('YmdHis');
    $created_by = $_SESSION['user_id'] ?? 0;

    $stmt = $conn->prepare("
        INSERT INTO sales 
        (invoice_no, bill_id, customer_id, subtotal, bill_discount, bill_tax, total, payment_method, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('ssiddidsi', $invoice_no, $bill_id, $customer_id, $subtotal, $bill_discount, $bill_tax, $total, $payment_method, $created_by);
    $stmt->execute();
    $sale_id = $stmt->insert_id;

    /* ---- INSERT ITEMS & UPDATE STOCK ---- */
    $itemStmt = $conn->prepare("INSERT INTO sales_items (sale_id, product_id, qty, price, discount, total) VALUES (?, ?, ?, ?, ?, ?)");
    $stockUpdateStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

    foreach ($cart as $item) {
        $p_id = $item['id'];
        $qty = $item['qty'];
        $price = $item['price'];
        $disc = $item['discount'] ?? 0;
        $line_total = ($price * $qty) - (($price * $qty) * $disc / 100);

        // Record Sale Item
        $itemStmt->bind_param('iiiddd', $sale_id, $p_id, $qty, $price, $disc, $line_total);
        $itemStmt->execute();

        // Deduct Inventory
        $stockUpdateStmt->bind_param('ii', $qty, $p_id);
        $stockUpdateStmt->execute();
    }

    /* ---------------- 5. COMMIT & FINISH ---------------- */
    $conn->commit();
    unset($_SESSION['cart']);

    echo json_encode([
        'status' => 1,
        'message' => 'Sale completed successfully!',
        'bill_id' => $bill_id,
        'invoice_no' => $invoice_no
    ]);

} catch (Exception $e) {
    // If any error occurs during the SQL execution, cancel everything
    $conn->rollback();
    echo json_encode([
        'status' => 0, 
        'message' => 'Critical Error: Could not save sale. ' . $e->getMessage()
    ]);
}