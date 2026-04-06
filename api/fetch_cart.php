<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Prepare response
$cart = [];
$total = 0;

foreach ($_SESSION['cart'] as $product_id => $item) {
    $price = floatval($item['price']);
    $qty = intval($item['qty']);
    $discount = floatval($item['discount'] ?? 0);

    $line_total = $price * $qty;
    $line_total -= $line_total * ($discount / 100);

    $cart[$product_id] = [
        'id' => $product_id,
        'name' => $item['name'],
        'price' => $price,        // keep numeric
        'qty' => $qty,
        'discount' => $discount,
        'total' => $line_total    // keep numeric
    ];

    $total += $line_total;
}

echo json_encode([
    'status' => 1,
    'cart' => $cart,
    'total' => $total
]);
