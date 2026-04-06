<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../config/db.php';

header('Content-Type: application/json');

$sale_id = intval($_POST['sale_id'] ?? 0);
$user_id = $_SESSION['user_id'] ?? 0;

if (!$sale_id) {
    echo json_encode(['status' => 0, 'message' => 'Invalid sale ID']);
    exit;
}

// Start Transaction
$conn->begin_transaction();

try {
    // 1. Check if sale exists and is not already refunded (Lock row for update)
    $stmt = $conn->prepare("SELECT id FROM sales WHERE id = ? AND is_refunded = 0 FOR UPDATE");
    $stmt->bind_param("i", $sale_id);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        throw new Exception('Sale not found or already refunded');
    }

    // 2. Get sale items
    $items = $conn->query("SELECT product_id, qty FROM sales_items WHERE sale_id = $sale_id");

    // 3. Refund stock
    $updateStock = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
    while ($item = $items->fetch_assoc()) {
        $qty = intval($item['qty']);
        $p_id = intval($item['product_id']);
        $updateStock->bind_param("ii", $qty, $p_id);
        $updateStock->execute();
    }

    // 4. Mark sale as refunded
    $markStmt = $conn->prepare("UPDATE sales SET is_refunded = 1, refunded_by = ?, refunded_at = NOW() WHERE id = ?");
    $markStmt->bind_param("ii", $user_id, $sale_id);
    $markStmt->execute();

    // 5. If everything reached here without error, save to DB
    $conn->commit();
    echo json_encode(['status' => 1, 'message' => 'Sale refunded successfully']);

} catch (Exception $e) {
    // If any step fails, undo everything
    $conn->rollback();
    echo json_encode(['status' => 0, 'message' => $e->getMessage()]);
}