<?php
header('Content-Type: application/json');
include_once '../config/db.php';

$phone = trim($conn->real_escape_string($_POST['phone'] ?? ''));

if(!$phone) {
    echo json_encode(['status'=>0, 'message'=>'Phone number is required']);
    exit;
}

$res = $conn->query("SELECT * FROM customers WHERE phone='$phone' LIMIT 1");

if($res->num_rows > 0){
    $c = $res->fetch_assoc();
    echo json_encode(['status'=>1, 'data'=>$c]);
} else {
    echo json_encode(['status'=>0, 'message'=>'Customer not found']);
}
