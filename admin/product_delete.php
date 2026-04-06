<?php
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';

if(!isset($_GET['id'])){
    echo "<script>alert('Product ID missing'); window.location='products.php';</script>";
    exit();
}

$id = intval($_GET['id']);

// Optional: Check if product exists
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
if(!$product){
    echo "<script>alert('Product not found'); window.location='products.php';</script>";
    exit();
}

// Delete product
$stmt = $conn->prepare("DELETE FROM products WHERE id=?");
$stmt->bind_param("i", $id);

if($stmt->execute()){
    echo "<script>alert('Product deleted successfully'); window.location='products.php';</script>";
} else {
    echo "<script>alert('Error deleting product'); window.location='products.php';</script>";
}
