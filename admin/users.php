<?php
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';
?>

<div class="container-fluid">
    <h2 class="mb-4">Users</h2>

    <div class="mb-3">
        <a href="user_add.php" class="btn btn-success">Add New User</a>
    </div>

    <!-- Filter Form -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by username or name" value="<?= $_GET['search'] ?? '' ?>">
                </div>
                <div class="col-md-4">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <?php
                        $roles = ['admin','cashier','manager']; // adjust as per your roles
                        foreach($roles as $r){
                            $selected = (isset($_GET['role']) && $_GET['role']==$r) ? 'selected' : '';
                            echo "<option value='$r' $selected>".ucfirst($r)."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="users.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $where = [];
                if(!empty($_GET['search'])){
                    $search = $conn->real_escape_string($_GET['search']);
                    $where[] = "(username LIKE '%$search%' OR name LIKE '%$search%')";
                }
                if(!empty($_GET['role'])){
                    $role = $conn->real_escape_string($_GET['role']);
                    $where[] = "role='$role'";
                }

                $where_sql = '';
                if(count($where) > 0){
                    $where_sql = 'WHERE ' . implode(' AND ', $where);
                }

                $query = "SELECT * FROM users $where_sql ORDER BY id ASC";
                $result = $conn->query($query);

                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['name']}</td>
                            <td>".ucfirst($row['role'])."</td>
                            <td>
                                <a href='user_edit.php?id={$row['id']}' class='btn btn-sm btn-primary'>Edit</a>
                                <a href='user_delete.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure?')\">Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No users found</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
