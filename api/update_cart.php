<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$product_id = intval($_POST['product_id'] ?? 0);
$qty = max(intval($_POST['qty'] ?? 1), 1);

if(!isset($_SESSION['cart'][$product_id])) {
    echo json_encode(['status'=>0, 'message'=>'Item not in cart']);
    exit;
}

// Update quantity
$_SESSION['cart'][$product_id]['qty'] = $qty;

// If qty is 0, remove item
if($qty <= 0) unset($_SESSION['cart'][$product_id]);

// Return updated cart
require_once 'fetch_cart.php';
