<?php
header('Content-Type: application/json');
include_once '../config/db.php';

// Collect and sanitize POST data
$name  = trim($conn->real_escape_string($_POST['name'] ?? ''));
$phone = trim($conn->real_escape_string($_POST['phone'] ?? ''));
$email = trim($conn->real_escape_string($_POST['email'] ?? ''));
$address = trim($conn->real_escape_string($_POST['address'] ?? ''));

// Basic validation
if(!$name || !$phone){
    echo json_encode(['status'=>0, 'message'=>'Name and phone are required']);
    exit;
}

// Check if phone already exists
$res = $conn->query("SELECT id FROM customers WHERE phone='$phone' LIMIT 1");
if($res->num_rows > 0){
    echo json_encode(['status'=>0, 'message'=>'Customer with this phone already exists']);
    exit;
}

// Insert new customer
$stmt = $conn->prepare("INSERT INTO customers (name, phone, email, address) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $phone, $email, $address);
if($stmt->execute()){
    $customer_id = $stmt->insert_id;
    echo json_encode(['status'=>1, 'data'=>['id'=>$customer_id, 'name'=>$name]]);
} else {
    echo json_encode(['status'=>0, 'message'=>'Failed to add customer']);
}
