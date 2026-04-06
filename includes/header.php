<?php
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($required_role)) $required_role = '';
$user = $_SESSION['user'] ?? null;
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../public/css/style.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background-color: #f4f6f9;
        }
        .navbar-brand i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/index.php">
                <i class="bi bi-shop"></i> POS System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <?php if($user): ?>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if($user['role'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link <?= $current_page=='dashboard.php'?'active':'' ?>" href="/admin/dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link <?= $current_page=='settings.php'?'active':'' ?>" href="/admin/settings.php"><i class="bi bi-gear"></i> Settings</a></li>
                        <li class="nav-item"><a class="nav-link <?= $current_page=='reports.php'?'active':'' ?>" href="/admin/reports.php"><i class="bi bi-file-earmark-text"></i> Reports</a></li>
                    <?php elseif($user['role'] === 'cashier'): ?>
                        <li class="nav-item"><a class="nav-link <?= $current_page=='dashboard.php'?'active':'' ?>" href="/cashier/dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link <?= $current_page=='pos.php'?'active':'' ?>" href="/cashier/pos.php"><i class="bi bi-cart4"></i> POS</a></li>
                        <li class="nav-item"><a class="nav-link <?= $current_page=='customers.php'?'active':'' ?>" href="/cashier/customers.php"><i class="bi bi-people"></i> Customers</a></li>
                        <li class="nav-item"><a class="nav-link <?= $current_page=='refunds.php'?'active':'' ?>" href="/cashier/refunds.php"><i class="bi bi-arrow-counterclockwise"></i> Refunds</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link text-warning fw-bold" href="/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </nav>

