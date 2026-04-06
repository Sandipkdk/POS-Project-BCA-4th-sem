<?php
$required_roles = ['admin', 'cashier'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

/**
 * 1. FILTER VALIDATION & SANITIZATION
 */
$where = [];

if (!empty($_GET['bill_id'])) {
    $bill_id = $conn->real_escape_string(trim($_GET['bill_id']));
    $where[] = "s.bill_id LIKE '%$bill_id%'";
}

if (!empty($_GET['invoice_no'])) {
    $invoice_no = $conn->real_escape_string(trim($_GET['invoice_no']));
    $where[] = "s.invoice_no LIKE '%$invoice_no%'";
}

if (!empty($_GET['customer_id'])) {
    $customer_id = intval($_GET['customer_id']);
    $where[] = "s.customer_id = $customer_id";
}

// Date Validation: Ensure start_date is before end_date logically
if (!empty($_GET['start_date'])) {
    $start = $conn->real_escape_string($_GET['start_date']);
    $where[] = "DATE(s.created_at) >= '$start'";
}

if (!empty($_GET['end_date'])) {
    $end = $conn->real_escape_string($_GET['end_date']);
    $where[] = "DATE(s.created_at) <= '$end'";
}

$where_sql = (count($where) > 0) ? 'WHERE ' . implode(' AND ', $where) : '';

/**
 * 2. DATA FETCHING
 */
$query = "SELECT s.*, c.name AS customer_name 
          FROM sales s
          LEFT JOIN customers c ON s.customer_id = c.id
          $where_sql
          ORDER BY s.created_at DESC
          LIMIT 100";
$result = $conn->query($query);
?>

<style>
    /* ... keep your existing styles ... */
    .badge-cash { background-color: #198754; }
    .badge-card { background-color: #0dcaf0; color: #000; }
    .badge-online { background-color: #6f42c1; }
</style>

<div class="container-fluid py-3">
    <h2 class="mb-4"><i class="bi bi-receipt me-2"></i>Invoices</h2>

    <div class="card filter-card mb-4 p-3 border-0 shadow-sm">
        <div class="invoices-section-title"><i class="bi bi-funnel me-2"></i> Filter Invoices</div>
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-bold small mb-1">Bill ID</label>
                <input type="text" name="bill_id" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['bill_id'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small mb-1">Invoice No</label>
                <input type="text" name="invoice_no" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['invoice_no'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small mb-1">Customer</label>
                <select name="customer_id" class="form-select form-select-sm">
                    <option value="">All Customers</option>
                    <?php
                    $customers = $conn->query("SELECT id, name FROM customers ORDER BY name ASC");
                    while($c = $customers->fetch_assoc()){
                        $selected = (isset($_GET['customer_id']) && $_GET['customer_id']==$c['id']) ? 'selected' : '';
                        echo "<option value='{$c['id']}' $selected>".htmlspecialchars($c['name'])."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small mb-1">Start Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $_GET['start_date'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small mb-1">End Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $_GET['end_date'] ?? '' ?>">
            </div>
            <div class="col-md-2 d-grid gap-1">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Filter</button>
                <a href="invoices.php" class="btn btn-light btn-sm border text-secondary"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="card mb-4 p-0 border-0 shadow-sm">
        <div class="p-3 invoices-section-title border-0 mb-0"><i class="bi bi-card-list me-2"></i> Recent Transactions</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">Bill ID</th>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Date & Time</th>
                        <th>Total Amount</th>
                        <th>Method</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if($result && $result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        // Payment Method Badge Logic
                        $method = strtolower($row['payment_method']);
                        $badge_class = "badge bg-secondary"; 
                        if($method == 'cash') $badge_class = "badge badge-cash";
                        if($method == 'card') $badge_class = "badge badge-card";

                        echo "<tr>
                            <td class='ps-3 fw-bold text-muted'>#{$row['bill_id']}</td>
                            <td><span class='text-primary'>{$row['invoice_no']}</span></td>
                            <td>".htmlspecialchars($row['customer_name'] ?? 'Walk-in Customer')."</td>
                            <td><small>".date('d M, Y', strtotime($row['created_at']))."<br><span class='text-muted'>".date('h:i A', strtotime($row['created_at']))."</span></small></td>
                            
                            <td class='fw-bold text-dark'>Rs. ".number_format($row['total'], 2)."</td>
                            
                            <td><span class='{$badge_class} text-uppercase' style='font-size:0.7rem;'>{$row['payment_method']}</span></td>
                            
                            <td class='text-center'>
                                <a href='invoice_view.php?id={$row['id']}' class='btn btn-sm btn-outline-primary shadow-sm'>
                                    <i class='bi bi-printer me-1'></i> View
                                </a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center text-muted py-5'>
                            <i class='bi bi-folder2-open fs-2 d-block'></i> No invoices match your search criteria.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>