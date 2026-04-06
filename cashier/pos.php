<?php
$required_roles = ['admin','cashier'];
include_once '../auth/auth_check.php';
include_once '../config/db.php';
include_once '../includes/header.php';
include_once '../includes/sidebar.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
?>

<div class="container-fluid py-3">

    <h2 class="mb-4"><i class="bi bi-cart-check me-2"></i> Point of Sale</h2>

    <div class="row">

        <!-- Product Search -->
        <div class="col-lg-6 mb-4">
            <div class="card p-3 shadow-sm">
                <h5 class="fw-bold mb-3"><i class="bi bi-search me-2"></i> Search Products</h5>
                <input type="text" id="product_search" class="form-control mb-2" placeholder="Search by name or code">
                <div id="search_results" class="border rounded p-2" style="max-height:400px; overflow-y:auto;"></div>
            </div>
        </div>

        <!-- Cart -->
        <div class="col-lg-6 mb-4">
            <div class="card p-3 shadow-sm sticky-top" style="top:10px;">

                <h5 class="fw-bold mb-3"><i class="bi bi-basket"></i> Cart</h5>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-striped" id="cart_table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>X</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- Customer -->
                <div class="mb-3">
                    <label class="fw-semibold">Customer Phone</label>
                    <input type="text" id="customer_phone" class="form-control" placeholder="Enter phone">
                    <input type="hidden" id="customer_id">
                    <small id="customer_info" class="text-success fw-semibold"></small>
                </div>

                <button class="btn btn-outline-primary w-100 mb-3" id="add_customer_btn" style="display:none" 
                        data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                    <i class="bi bi-person-plus me-2"></i> Add New Customer
                </button>

                <!-- Discount & Tax -->
                <div class="row mb-3">
                    <div class="col">
                        <label class="fw-semibold">Discount</label>
                        <div class="input-group">
                            <input type="number" id="bill_discount_value" class="form-control" value="0" min="0">
                            <select id="bill_discount_type" class="form-select">
                                <option value="percent">%</option>
                                <option value="fixed">Rs</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <label class="fw-semibold">Tax (%)</label>
                        <input type="number" id="bill_tax" class="form-control" value="0" min="0">
                    </div>
                </div>

                <!-- Total -->
                <div class="bg-primary text-white p-3 rounded mb-3">
                    <h4>Total: Rs. <span id="total_amount">0.00</span></h4>
                </div>

                <!-- Payment -->
                <div class="mb-3">
                    <label class="fw-semibold">Payment Method</label>
                    <select id="payment_method" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                    </select>
                </div>

                <button class="btn btn-success w-100 py-2 fs-5" id="save_sale">
                    <i class="bi bi-check2-circle me-2"></i> Complete Sale
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="add_customer_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i> Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-semibold">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control" required readonly>
                    </div>
                    <div class="mb-3">
                        <label class="fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="fw-semibold">Address</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary w-100">Add Customer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../public/js/pos.js"></script>
<?php include '../includes/footer.php'; ?>
