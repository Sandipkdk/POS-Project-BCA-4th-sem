<?php
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Invalid request'); window.location='users.php';</script>";
    exit();
}

$id = intval($_POST['id']);

// Fetch the user
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

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('User deleted successfully'); window.location='users.php';</script>";
} else {
    echo "<script>alert('Error deleting user'); window.location='users.php';</script>";
}
