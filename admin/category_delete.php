<?php
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';

if(!isset($_GET['id'])){
    echo "<script>alert('Category ID missing'); window.location='categories.php';</script>";
    exit();
}

$id = intval($_GET['id']);

// Optional: Check if any products are using this category
$product_check = $conn->query("SELECT COUNT(*) as count FROM products WHERE category_id=$id")->fetch_assoc();
if($product_check['count'] > 0){
    echo "<script>alert('Cannot delete category. There are products using it.'); window.location='categories.php';</script>";
    exit();
}

// Delete category
$stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
$stmt->bind_param("i", $id);

if($stmt->execute()){
    echo "<script>alert('Category deleted successfully'); window.location='categories.php';</script>";
} else {
    echo "<script>alert('Error deleting category'); window.location='categories.php';</script>";
}
