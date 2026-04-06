<?php
$required_roles = ['admin','cashier'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    if($name && $phone){
        // Check phone uniqueness
        $exists = $conn->query("SELECT id FROM customers WHERE phone='$phone' LIMIT 1");
        if($exists->num_rows > 0){
            $error = "Phone number already exists";
        } else {
            $stmt = $conn->prepare("INSERT INTO customers (name, phone, email, address) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $phone, $email, $address);
            if($stmt->execute()){
                header("Location: customers.php");
                exit;
            } else {
                $error = "Failed to add customer";
            }
        }
    } else {
        $error = "Name and phone are required";
    }
}

include_once '../includes/header.php';
include_once '../includes/sidebar.php';


?>

<div class="container-fluid">
    <h2 class="mb-4">Add Customer</h2>
    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Add Customer</button>
        <a href="customers.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
