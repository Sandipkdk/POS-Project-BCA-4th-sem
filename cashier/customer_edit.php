<?php
$required_roles = ['admin','cashier'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

$id = intval($_GET['id']);
$customer = $conn->query("SELECT * FROM customers WHERE id=$id")->fetch_assoc();
if(!$customer) exit("Customer not found");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    if($name && $phone){
        // Check phone uniqueness excluding current customer
        $exists = $conn->query("SELECT id FROM customers WHERE phone='$phone' AND id<>$id LIMIT 1");
        if($exists->num_rows > 0){
            $error = "Phone number already exists";
        } else {
            $stmt = $conn->prepare("UPDATE customers SET name=?, phone=?, email=?, address=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $phone, $email, $address, $id);
            if($stmt->execute()){
                header("Location: customers.php");
                exit;
            } else {
                $error = "Failed to update customer";
            }
        }
    } else {
        $error = "Name and phone are required";
    }
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Edit Customer</h2>
    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($customer['name']) ?>">
        </div>
        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($customer['phone']) ?>">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email']) ?>">
        </div>
        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control"><?= htmlspecialchars($customer['address']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Customer</button>
        <a href="customers.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
