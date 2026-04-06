<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'includes/header.php';

// Redirect if already logged in (currently disabled)
// if(isset($_SESSION['user_id'])){
//     if($_SESSION['role'] === 'admin'){
//         header('Location: admin/dashboard.php');
//         exit;
//     } elseif($_SESSION['role'] === 'cashier'){
//         header('Location: cashier/dashboard.php');
//         exit;
//     }
// }
?>

<!-- Landing Page -->
<div class="vh-100 d-flex justify-content-center align-items-center position-relative" style="background-color:#1f1f2e; overflow:hidden;">

    <!-- Background icons pattern -->
    <div class="position-absolute w-100 h-100" style="z-index:0; overflow:hidden;">
        <?php for($i=0;$i<50;$i++): ?>
            <i class="bi bi-cash-stack" style="position:absolute; font-size:24px; color:rgba(255,255,255,0.1); 
                top:<?= rand(0,100) ?>%; left:<?= rand(0,100) ?>%; transform:rotate(<?= rand(0,360) ?>deg);"></i>
        <?php endfor; ?>
    </div>

    <div class="text-center text-white" style="z-index:1;">
        <h1 class="display-4 fw-bold mb-4"><i class="bi bi-speedometer2 me-2"></i>Welcome to POS System<i class="bi bi-shop"></i></h1>
        <p class="lead mb-4">Manage your sales, customers, and inventory seamlessly.</p>
        <a href="auth/login.php" class="btn btn-lg btn-primary px-5 py-3 shadow">
            <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </a>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
