<?php
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('User ID missing'); window.location='users.php';</script>";
    exit();
}

$id = intval($_GET['id']);

// Fetch user
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
if (!$user) {
    echo "<script>alert('User not found'); window.location='users.php';</script>";
    exit();
}

// Prevent deleting yourself
if ($id == $_SESSION['user_id']) {
    echo "<script>alert('You cannot delete your own account'); window.location='users.php';</script>";
    exit();
}

// Prevent deleting last admin
if ($user['role'] == 'admin') {
    $admins = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='admin'")->fetch_assoc();
    if ($admins['total'] <= 1) {
        echo "<script>alert('Cannot delete the last admin account'); window.location='users.php';</script>";
        exit();
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="container-fluid">
    <h2 class="mb-4 text-danger"><i class="bi bi-trash-fill me-2"></i>Delete User</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5>Are you sure you want to delete this user?</h5>

            <p>
                <strong>Username:</strong> <?= htmlspecialchars($user['username']) ?><br>
                <strong>Name:</strong> <?= htmlspecialchars($user['name']) ?><br>
                <strong>Role:</strong> <?= htmlspecialchars($user['role']) ?><br>
            </p>

            <form method="POST" action="user_delete_confirm.php">
                <input type="hidden" name="id" value="<?= $id ?>">

                <button class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i> Yes, Delete User
                </button>

                <a href="users.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left-circle me-1"></i> Cancel
                </a>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
