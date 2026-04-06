<?php
$required_roles = ['admin', 'cashier'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

/**
 * SERVER-SIDE VALIDATION & FILTERS
 */
$where = [];
$search_val = '';
$cat_val = '';

if (!empty($_GET['search'])) {
    $search_val = trim($_GET['search']);
    $search = $conn->real_escape_string($search_val);
    $where[] = "p.name LIKE '%$search%'";
}

if (!empty($_GET['category_id'])) {
    $cat_val = intval($_GET['category_id']);
    $where[] = "p.category_id = $cat_val";
}

$where_sql = (count($where) > 0) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "SELECT p.*, c.name AS category_name
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          $where_sql
          ORDER BY p.id DESC
          LIMIT 200";

$result = $conn->query($query);
?>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-box-seam me-2"></i> Product Management</h2>
        <a href="product_add.php" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Add New Product
        </a>
    </div>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body bg-light rounded">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-bold small text-uppercase">Search Product</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name..." value="<?= htmlspecialchars($search_val) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        <?php
                        $categories = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
                        while ($c = $categories->fetch_assoc()) {
                            $selected = ($cat_val == $c['id']) ? 'selected' : '';
                            echo "<option value='{$c['id']}' $selected>" . htmlspecialchars($c['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary w-100 me-2">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    <a href="products.php" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th class="text-center">Stock</th>
                        <th>Discount</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Data Sanitization
                        $name = htmlspecialchars($row['name']);
                        $desc = htmlspecialchars($row['description'] ?? '');
                        $cat  = htmlspecialchars($row['category_name'] ?? 'Uncategorized');
                        
                        // Numeric Validation (No Negatives)
                        $price    = max(0, (float)$row['price']);
                        $stock    = (int)$row['stock'];
                        $discount = max(0, (float)$row['product_discount']);

                        // Stock Logic for Color Coding
                        if ($stock <= 0) {
                            $stock_badge = '<span class="badge bg-danger">Out of Stock</span>';
                        } elseif ($stock <= 10) {
                            $stock_badge = '<span class="badge bg-warning text-dark">Low: ' . $stock . '</span>';
                        } else {
                            $stock_badge = '<span class="badge bg-success">' . $stock . ' Units</span>';
                        }

                        echo "<tr>
                            <td class='ps-3 text-muted'>#{$row['id']}</td>
                            <td>
                                <div class='fw-bold text-primary'>$name</div>
                                <div class='small text-muted text-truncate' style='max-width: 200px;'>$desc</div>
                            </td>
                            <td><span class='badge bg-light text-dark border'>$cat</span></td>
                            <td class='fw-bold text-dark'>Rs. " . number_format($price, 2) . "</td>
                            <td class='text-center'>$stock_badge</td>
                            <td><span class='text-danger fw-bold'>{$discount}%</span></td>
                            <td class='text-end pe-3'>
                                <div class='btn-group shadow-sm'>
                                    <a href='product_edit.php?id={$row['id']}' class='btn btn-sm btn-white border' title='Edit'>
                                        <i class='bi bi-pencil-square text-primary'></i>
                                    </a>
                                    <a href='product_delete.php?id={$row['id']}' class='btn btn-sm btn-white border' 
                                       onclick=\"return confirm('Are you sure you want to delete $name?')\" title='Delete'>
                                        <i class='bi bi-trash text-danger'></i>
                                    </a>
                                </div>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center py-5 text-muted'>
                            <i class='bi bi-search fs-1 d-block mb-2'></i>
                            No products found matching your criteria.
                          </td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>