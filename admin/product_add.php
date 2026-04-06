<?php
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

$error = "";
$success = "";

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // 1. Sanitize and Validate Inputs
    $name = trim($conn->real_escape_string($_POST['name']));
    $category_id = intval($_POST['category_id']);
    $description = trim($conn->real_escape_string($_POST['description']));
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $discount = floatval($_POST['discount']); // Fixed name mismatch

    // 2. Backend Validation Rules
    if (empty($name)) {
        $error = "Product name is required.";
    } elseif ($price < 0) {
        $error = "Price cannot be a negative value.";
    } elseif ($stock < 0) {
        $error = "Stock cannot be negative.";
    } elseif ($discount < 0 || $discount > 100) {
        $error = "Discount must be between 0 and 100%.";
    } elseif ($category_id <= 0) {
        $error = "Please select a valid category.";
    }

    // 3. If no errors, proceed to Database
    if(empty($error)){
        $stmt = $conn->prepare("INSERT INTO products (name, category_id, description, price, stock, product_discount) VALUES (?, ?, ?, ?, ?, ?)");
        // "sisddd" -> string, int, string, double, double, double
        $stmt->bind_param("sisddd", $name, $category_id, $description, $price, $stock, $discount);

        if($stmt->execute()){
            echo "<script>alert('✅ Product added successfully!'); window.location='products.php';</script>";
            exit();
        } else {
            $error = "Database Error: " . $conn->error;
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
                <a href="products.php" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Add New Product</h2>
            </div>

            <?php if(!empty($error)): ?>
                <div class="alert alert-danger shadow-sm">
                    <i class="bi bi-exclamation-triangle me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Product Name</label>
                                <input type="text" name="name" class="form-control" 
                                       placeholder="Enter product name" required autofocus>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Choose Category --</option>
                                    <?php while($c = $categories->fetch_assoc()): ?>
                                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Description <small class="text-muted">(Optional)</small></label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Add product details..."></textarea>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Price (Rs.)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" step="0.01" min="0" name="price" 
                                           class="form-control" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Initial Stock</label>
                                <input type="number" min="0" name="stock" 
                                       class="form-control" placeholder="0" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Discount (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" min="0" max="100" name="discount" 
                                           class="form-control" value="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="products.php" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-success px-5 shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Save Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>