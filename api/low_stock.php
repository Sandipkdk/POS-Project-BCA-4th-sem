<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../config/db.php';

if(!isset($_SESSION['user_id'])){
    echo json_encode(['status'=>'error','message'=>'Unauthorized']);
    exit();
}

// Define low stock threshold (can also make it configurable in admin settings)
$threshold = 5;

$result = $conn->query("SELECT id, name, stock FROM products WHERE stock <= $threshold ORDER BY stock ASC");

$low_stock_items = [];
while($row = $result->fetch_assoc()){
    $low_stock_items[] = $row;
}

echo json_encode(['status'=>'success', 'items'=>$low_stock_items]);
