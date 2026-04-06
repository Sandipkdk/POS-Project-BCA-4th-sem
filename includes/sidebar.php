<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../config/db.php';

$user_id = $_SESSION['user_id'] ?? 0;
$user = null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sidebar</title>

<style>
/* BASIC SIDEBAR - BEGINNER STYLE */

#sidebar-wrapper {
    width: 220px;
    background-color: #222;
    color: #ffffff;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
    border-bottom: 1px solid #444;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu li a {
    display: block;
    padding: 10px 15px;
    color: #dddddd;
    text-decoration: none;
    font-size: 15px;
}

.sidebar-menu li a:hover {
    background-color: #333;
}

.sidebar-menu li a.active {
    background-color: #0d6efd;
    color: #ffffff;
}

.sidebar-footer {
    margin-top: auto;
    padding: 15px;
    border-top: 1px solid #444;
    font-size: 14px;
}

.logout-link {
    display: block;
    margin-top: 10px;
    color: #ff6b6b;
    text-decoration: none;
}

.logout-link:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="d-flex" id="wrapper">

    <!-- SIDEBAR -->
    <div id="sidebar-wrapper">

        <div class="sidebar-header">
            POS System
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="../<?php echo ($user['role']=='admin') ? 'admin' : 'cashier'; ?>/dashboard.php"
                   class="<?= $current_page=='dashboard.php' ? 'active' : '' ?>">
                    Dashboard
                </a>
            </li>

            <li>
                <a href="../cashier/pos.php"
                   class="<?= $current_page=='pos.php' ? 'active' : '' ?>">
                    Create Order
                </a>
            </li>

            <li>
                <a href="../cashier/invoices.php"
                   class="<?= $current_page=='invoices.php' ? 'active' : '' ?>">
                    Orders
                </a>
            </li>

            <li>
                <a href="../cashier/customers.php"
                   class="<?= $current_page=='customers.php' ? 'active' : '' ?>">
                    Customers
                </a>
            </li>

            <?php if ($user['role'] == 'admin'): ?>
            <li>
                <a href="../admin/products.php"
                   class="<?= $current_page=='products.php' ? 'active' : '' ?>">
                    Products
                </a>
            </li>

            <li>
                <a href="../admin/categories.php"
                   class="<?= $current_page=='categories.php' ? 'active' : '' ?>">
                    Categories
                </a>
            </li>

            <li>
                <a href="../admin/users.php"
                   class="<?= $current_page=='users.php' ? 'active' : '' ?>">
                    Users
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <div class="sidebar-footer">
            <div>Logged in as</div>
            <strong><?= htmlspecialchars($user['username']) ?></strong>

            <a href="../auth/logout.php" class="logout-link">Logout</a>
        </div>

    </div>
    <!-- END SIDEBAR -->

    <!-- PAGE CONTENT -->
    <div id="page-content-wrapper" style="flex:1; padding:20px;">
