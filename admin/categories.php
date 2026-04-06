<?php
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';
?>

<div class="container-fluid py-3">

    <h2 class="mb-4"><i class="bi bi-tags me-2"></i>Categories</h2>

    <div class="mb-3">
        <a href="category_add.php" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Add New Category
        </a>
    </div>

    <div class="card pos-card p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="25%">Name</th>
                        <th width="50%">Description</th>
                        <th width="20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>".(!empty($row['description']) ? htmlspecialchars($row['description']) : '<span class="text-muted">No description</span>')."</td>
                            <td>
                                <a href='category_edit.php?id={$row['id']}' class='btn btn-sm btn-primary me-1'>
                                    <i class='bi bi-pencil-square'></i> Edit
                                </a>
                                <a href='category_delete.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this category?')\">
                                    <i class='bi bi-trash'></i> Delete
                                </a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center text-muted py-3'>No categories found</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
