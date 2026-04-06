<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../config/db.php';

$query = trim($_POST['query'] ?? '');

if (strlen($query) < 2) {
    echo '';
    exit;
}

// Search by name or partial id
$stmt = $conn->prepare("
    SELECT id, name, price, stock 
    FROM products 
    WHERE name LIKE ? OR CAST(id AS CHAR) LIKE ?
    ORDER BY name ASC
    LIMIT 15
");

$search = "%$query%";
$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {

    while ($p = $res->fetch_assoc()) {
        ?>
        <div class="item d-flex justify-content-between align-items-center">
            <div>
                <strong><?= htmlspecialchars($p['name']) ?></strong><br>
                <small class="text-muted">Stock: <?= $p['stock'] ?></small>
            </div>

            <button 
                class="btn btn-sm btn-primary add-to-cart"
                data-id="<?= $p['id'] ?>"
                data-name="<?= htmlspecialchars($p['name']) ?>"
                data-price="<?= $p['price'] ?>"
            >
                Add
            </button>
        </div>
        <?php
    }

} else {
    echo '<div class="text-muted p-2">No items found</div>';
}
