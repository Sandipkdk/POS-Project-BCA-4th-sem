<?php
/**
 * SYSTEM SETTINGS
 * Configuration for Tax, Discounts, and Company Branding.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Security Check: Admin and Cashier access (adjust roles as needed)
$required_roles = ['admin']; 
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

$message = "";

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and Validate Inputs
    $tax = filter_input(INPUT_POST, 'tax', FILTER_VALIDATE_FLOAT);
    $discount = filter_input(INPUT_POST, 'discount', FILTER_VALIDATE_FLOAT);
    $company_name = trim($_POST['company_name'] ?? '');

    // Validation Rules
    if ($tax === false || $tax < 0 || $tax > 100) {
        $message = "<div class='alert alert-danger'>Invalid Tax percentage (0-100).</div>";
    } elseif ($discount === false || $discount < 0 || $discount > 100) {
        $message = "<div class='alert alert-danger'>Invalid Discount percentage (0-100).</div>";
    } elseif (empty($company_name)) {
        $message = "<div class='alert alert-danger'>Company Name cannot be empty.</div>";
    } else {
        // 3. Update using Prepared Statements for security
        $stmt = $conn->prepare("UPDATE settings SET tax = ?, discount = ?, company_name = ? LIMIT 1");
        $stmt->bind_param("dds", $tax, $discount, $company_name);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>✅ Settings updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Error updating settings: " . $conn->error . "</div>";
        }
    }
}

// 4. Fetch current settings
$settings_res = $conn->query("SELECT * FROM settings LIMIT 1");
$settings = $settings_res->fetch_assoc();

// If no settings exist yet, provide defaults to avoid PHP errors
if (!$settings) {
    $settings = ['tax' => 0, 'discount' => 0, 'company_name' => 'My Business'];
}
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0"><i class="bi bi-gear-fill me-2 text-primary"></i>System Settings</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?= $message ?>

                    <form method="POST" action="">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Company / Store Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <input type="text" name="company_name" class="form-control" 
                                       value="<?= htmlspecialchars($settings['company_name']) ?>" required>
                            </div>
                            <small class="text-muted">This name will appear on all printed invoices.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Default Tax (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="tax" class="form-control" 
                                           value="<?= $settings['tax'] ?>" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Applied automatically to new bills.</small>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Default Discount (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="discount" class="form-control" 
                                           value="<?= $settings['discount'] ?>" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Default markdown for all items.</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-light border px-4">Reset Changes</button>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-check-circle me-1"></i> Save Configuration
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>