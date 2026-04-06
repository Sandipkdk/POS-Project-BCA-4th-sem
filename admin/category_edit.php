<?php
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

if(!isset($_GET['id'])){
    echo "Category ID missing";
    exit();
}

$id = intval($_GET['id']);
$category = $conn->query("SELECT * FROM categories WHERE id=$id")->fetch_assoc();
if(!$category){
    echo "Category not found";
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);

    $stmt = $conn->prepare("UPDATE categories SET name=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $description, $id);

    if($stmt->execute()){
        echo "<script>alert('Category updated successfully'); window.location='categories.php';</script>";
        exit();
    } else {
        $error = "Error updating category: " . $conn->error;
    }
}
?>

<div class="container-fluid py-3">

    <h2 class="mb-4"><i class="bi bi-pencil-square me-2"></i> Edit Category</h2>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card pos-card p-3">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Category Name</label>
                <input type="text" name="name" class="form-control" 
                       value="<?= htmlspecialchars($category['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" 
                          placeholder="Enter description (optional)"><?= htmlspecialchars($category['description']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary me-2">
                <i class="bi bi-check-circle me-1"></i> Update Category
            </button>
            <a href="categories.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle me-1"></i> Back
            </a>
        </form>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
