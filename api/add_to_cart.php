<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
include_once '../config/db.php';

/* ---------------- 1. DATA VALIDATION ---------------- */
$product_id = intval($_POST['product_id'] ?? 0);
$requested_qty = intval($_POST['qty'] ?? 1);

if ($product_id <= 0) {
    echo json_encode(['status' => 0, 'message' => 'Invalid Product Selection.']);
    exit;
}

/* ---------------- 2. FETCH PRODUCT & STOCK ---------------- */
$stmt = $conn->prepare("SELECT id, name, price, stock FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo json_encode(['status' => 0, 'message' => 'Product not found in database.']);
    exit;
}

$available_stock = intval($product['stock']);

/* ---------------- 3. STOCK VALIDATION ---------------- */
if ($available_stock <= 0) {
    echo json_encode(['status' => 0, 'message' => "Out of Stock: '{$product['name']}' is currently unavailable."]);
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Calculate what the new quantity would be
$current_in_cart = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['qty'] : 0;
$new_total_qty = $current_in_cart + $requested_qty;

// Final stock barrier
if ($new_total_qty > $available_stock) {
    echo json_encode([
        'status' => 0, 
        'message' => "Insufficient Stock: You already have {$current_in_cart} in cart. Only " . ($available_stock - $current_in_cart) . " more can be added."
    ]);
    exit;
}

/* ---------------- 4. UPDATE SESSION ---------------- */
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['qty'] = $new_total_qty;
} else {
    $_SESSION['cart'][$product_id] = [
        'id'       => $product['id'],
        'name'     => $product['name'],
        'price'    => $product['price'],
        'qty'      => $new_total_qty,
        'stock'    => $available_stock, // We store this to help the JS UI
        'discount' => 0
    ];
}

/* ---------------- 5. RETURN UPDATED CART ---------------- */
// Important: Ensure fetch_cart.php returns JSON correctly
require_once 'fetch_cart.php';