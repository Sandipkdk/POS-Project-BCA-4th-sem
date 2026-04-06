<?php
$required_role = 'cashier';
include_once '../auth/auth_check.php';
include_once '../config/db.php';

$customer_id = intval($_GET['customer_id'] ?? 0);

if(!$customer_id){
    echo json_encode(['status'=>0, 'message'=>'Invalid customer ID']);
    exit;
}

// Fetch sales for this customer
$res = $conn->query("SELECT s.id, s.bill_id, s.invoice_no, s.total, s.payment_method, s.created_at
                     FROM sales s
                     WHERE s.customer_id=$customer_id
                     ORDER BY s.created_at DESC
                     LIMIT 100");

$invoices = [];
while($row = $res->fetch_assoc()){
    $invoices[] = $row;
}

echo json_encode(['status'=>1, 'data'=>$invoices]);
