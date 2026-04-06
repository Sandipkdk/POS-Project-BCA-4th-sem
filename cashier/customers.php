<?php
$required_roles = ['admin','cashier'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';
?>

<div class="container-fluid">
    <h2 class="mb-4">Customers</h2>

    <div class="mb-3">
        <a href="customer_add.php" class="btn btn-success">Add New Customer</a>
    </div>

    <form method="GET" class="mb-3 row g-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name or phone" value="<?= $_GET['search'] ?? '' ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $where = '';
            if(!empty($_GET['search'])){
                $s = $conn->real_escape_string($_GET['search']);
                $where = "WHERE name LIKE '%$s%' OR phone LIKE '%$s%'";
            }

            $res = $conn->query("SELECT * FROM customers $where ORDER BY name ASC");
            if($res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['address']}</td>
                        <td>" . date("M d, Y h:i A", strtotime($row['created_at'])) . "</td>
                        <td>
                            <a href='customer_edit.php?id={$row['id']}' class='btn btn-sm btn-primary'>Edit</a>
                            <a href='customer_purchases.php?id={$row['id']}' class='btn btn-sm btn-info'>Purchases</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No customers found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
