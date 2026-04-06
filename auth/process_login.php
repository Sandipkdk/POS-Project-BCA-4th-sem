<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../config/db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    $_SESSION['login_error'] = "Username and password required";
    header("Location: login.php");
    exit;
}

// Fetch user
$stmt = $conn->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['login_error'] = "Incorrect username or password";
    header("Location: login.php");
    exit;
}

$user = $result->fetch_assoc();

// Check password
$login_success = false;

// Try hashed password first
if (password_verify($password, $user['password'])) {
    $login_success = true;
} 
// If that fails, check plain text match
elseif ($user['password'] === $password) {
    $login_success = true;
}

if ($login_success) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Redirect based on role
    if ($user['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../cashier/dashboard.php");
    }
    exit;
} else {
    $_SESSION['login_error'] = "Incorrect username or password";
    header("Location: login.php");
    exit;
}
