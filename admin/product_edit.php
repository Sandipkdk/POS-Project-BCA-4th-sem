<?php
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

if(!isset($_GET['id'])){
    echo "<div class='alert alert-danger'>Error: Product ID missing</div>";
    exit();
}

$id = intval($_GET['id']);
$error = "";

// Fetch product data
$stmt_fetch = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt_fetch->bind_param("i", $id);
$stmt_fetch->execute();
$product = $stmt_fetch->get_result()->fetch_assoc();

if(!$product){
    echo "<div class='alert alert-danger'>Error: Product not found</div>";
    exit();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // 1. Sanitize Inputs
    $name = trim($conn->real_escape_string($_POST['name']));
    $category_id = intval($_POST['category_id']);
    $description = trim($conn->real_escape_string($_POST['description']));
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $discount = floatval($_POST['discount']); // Fixed name mismatch

    // 2. Validation Logic
    if (empty($name)) {
        $error = "Product name cannot be empty.";
    } elseif ($price < 0) {
        $error = "Price cannot be negative.";
    } elseif ($stock < 0) {
        $error = "Stock cannot be negative.";
    } elseif ($discount < 0 || $discount > 100) {
        $error = "Discount must be between 0% and 100%.";
    }

    // 3. Update Database if validation passes
    if(empty($error)){
        $stmt = $conn->prepare("UPDATE products SET name=?, category_id=?, description=?, price=?, stock=?, product_discount=? WHERE id=?");
        // Bind types: s=string, i=int, d=double
        $stmt->bind_param("sisdddi", $name, $category_id, $description, $price, $stock, $discount, $id);

        if($stmt->execute()){
            echo "<script>alert('✅ Product updated successfully'); window.location='products.php';</script>";
            exit();
        } else {
            $error = "Update failed: " . $conn->error;
        }
    }
}

// Fetch categories for dropdown
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-4">
                <a href="products.php" class="btn btn-outline-secondary me-3 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Edit Product (#<?= $id ?>)</h2>
            </div>

            <?php if(!empty($error)): ?>
                <div class="alert alert-danger shadow-sm border-start border-4 border-danger">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Product Name</label>
                                <input type="text" name="name" class="form-control" 
                                       value="<?= htmlspecialchars($product['name']) ?>" required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <?php while($c = $categories->fetch_assoc()): ?>
                                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c['name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" step="0.01" min="0" name="price" 
                                           class="form-control" value="<?= $product['price'] ?>" required>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Stock</label>
                                <input type="number" min="0" name="stock" 
                                       class="form-control" value="<?= $product['stock'] ?>" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Discount (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" min="0" max="100" name="discount" 
                                           class="form-control" value="<?= $product['product_discount'] ?>">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="products.php" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                <i class="bi bi-save me-1"></i> Update Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>