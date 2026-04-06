<?php
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);

    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);

    if($stmt->execute()){
        echo "<script>alert('Category added successfully'); window.location='categories.php';</script>";
        exit();
    } else {
        $error = "Error adding category: " . $conn->error;
    }
}
?>

<div class="container-fluid py-3">

    <h2 class="mb-4"><i class="bi bi-plus-circle me-2"></i> Add Category</h2>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card pos-card p-3">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Category Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter category name" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" placeholder="Enter description (optional)"></textarea>
            </div>

            <button type="submit" class="btn btn-success me-2">
                <i class="bi bi-check-circle me-1"></i> Add Category
            </button>
            <a href="categories.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle me-1"></i> Back
            </a>
        </form>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
