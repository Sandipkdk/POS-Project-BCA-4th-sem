<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../includes/header.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin/dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'cashier') {
        header('Location: ../cashier/dashboard.php');
        exit;
    }
}

// Capture and clear any login error
$login_error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<!-- Login Page -->
<div class="vh-100 d-flex justify-content-center align-items-center position-relative" style="background-color:#1f1f2e; overflow:hidden;">

    <!-- Background icons pattern -->
    <div class="position-absolute w-100 h-100" style="z-index:0; overflow:hidden;">
        <?php for ($i=0;$i<50;$i++): ?>
            <i class="bi bi-cash-stack" style="position:absolute; font-size:24px; color:rgba(255,255,255,0.1); 
               top:<?= rand(0,100) ?>%; left:<?= rand(0,100) ?>%; transform:rotate(<?= rand(0,360) ?>deg);"></i>
        <?php endfor; ?>
    </div>

    <div class="card shadow-lg p-5 rounded-4 text-center" style="width: 350px; z-index:1; background-color:#2c2c3c;">
        <h2 class="mb-4 text-white"><i class="bi bi-speedometer2 me-2"></i>POS Login</h2>

        <?php if($login_error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($login_error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="../auth/process_login.php" method="POST" class="text-start">
            <div class="mb-3">
                <label for="username" class="form-label text-white">Username</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label text-white">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
