<?php
$required_roles = ['admin'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

if (!isset($_GET['id'])) {
    echo "User ID missing";
    exit();
}

$id = intval($_GET['id']);
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();

if (!$user) {
    echo "User not found";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $role = $_POST['role'];

    $sql = "UPDATE users SET name=?, role=?";
    $params = [$name, $role];
    $types = "ss";

    // Update password only if new password entered
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql .= ", password=?";
        $types .= "s";
        $params[] = $password;
    }

    $sql .= " WHERE id=?";
    $types .= "i";
    $params[] = $id;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully'); window.location='users.php';</script>";
        exit();
    } else {
        $error = "Error updating user: " . $conn->error;
    }
}
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-pencil-square me-2"></i> Edit User</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST">

                <div class="mb-3">
                    <label class="fw-semibold">Username <small class="text-muted">(cannot change)</small></label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Full Name</label>
                    <input type="text" name="name" 
                           class="form-control"
                           placeholder="Enter full name"
                           value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">New Password 
                        <small class="text-muted">(leave blank to keep current)</small>
                    </label>
                    <input type="password" name="password" 
                           class="form-control" 
                           placeholder="Enter new password (optional)">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="cashier" <?= $user['role'] == 'cashier' ? 'selected' : '' ?>>Cashier</option>
                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Update User
                    </button>
                    <a href="users.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left-circle me-1"></i> Back
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
