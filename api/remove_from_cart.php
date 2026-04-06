<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$product_id = intval($_POST['product_id'] ?? 0);

if(isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

// Return updated cart
require_once 'fetch_cart.php';
