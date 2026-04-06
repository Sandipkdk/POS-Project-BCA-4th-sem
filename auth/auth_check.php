<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If not logged in → redirect
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

if (isset($required_roles)) {

    // If it's a single role, convert to array
    if (!is_array($required_roles)) {
        $required_roles = [$required_roles];
    }

    // If user role NOT inside the allowed list
    if (!in_array($_SESSION['role'], $required_roles)) {
        echo "Access denied.";
        exit();
    }
}
