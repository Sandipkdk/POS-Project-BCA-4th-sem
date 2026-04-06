<?php
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $conn->real_escape_string($_POST['username']);
    $name = $conn->real_escape_string($_POST['name']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Check if username exists
    $check = $conn->query("SELECT * FROM users WHERE username='$username'");
    if($check->num_rows > 0){
        $error = "Username already exists";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, name, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $name, $password, $role);
        if($stmt->execute()){
            echo "<script>alert('User added successfully'); window.location='users.php';</script>";
            exit();
        } else {
            $error = "Error adding user: " . $conn->error;
        }
    }
}
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-person-plus me-2"></i> Add User</h2>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="fw-semibold">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                </div>
                <div class="mb-3">
                    <label class="fw-semibold">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                </div>
                <div class="mb-3">
                    <label class="fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>
                <div class="mb-3">
                    <label class="fw-semibold">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="cashier">Cashier</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Add User</button>
                    <a href="users.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-1"></i> Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
