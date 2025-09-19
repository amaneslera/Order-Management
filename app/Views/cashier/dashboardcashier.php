<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-shop"></i> Order Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= site_url('cashier') ?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('cashier/orders') ?>">
                            <i class="bi bi-cart3"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('cashier/products') ?>">
                            <i class="bi bi-box-seam"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('barcode-master/scan.php') ?>">
                            <i class="bi bi-camera"></i> Scan Barcode
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= session()->get('username') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= site_url('logout') ?>">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Success Message -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h2><i class="bi bi-speedometer2"></i> Cashier Dashboard</h2>
                <p class="text-muted">Welcome back, <?= session()->get('username') ?>!</p>
            </div>
            <div class="col-auto">
                <a href="<?= site_url('cashier/orders/new') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> New Order
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Today's Orders</h6>
                                <h2 class="mb-0">24</h2>
                            </div>
                            <i class="bi bi-cart4 fs-1"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="<?= site_url('cashier/orders') ?>" class="small text-white stretched-link">View Details</a>
                        <i class="bi bi-chevron-right text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Sales</h6>
                                <h2 class="mb-0">$1,258</h2>
                            </div>
                            <i class="bi bi-currency-dollar fs-1"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="<?= site_url('cashier/sales') ?>" class="small text-white stretched-link">View Details</a>
                        <i class="bi bi-chevron-right text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Pending Orders</h6>
                                <h2 class="mb-0">5</h2>
                            </div>
                            <i class="bi bi-hourglass-split fs-1"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="<?= site_url('cashier/orders/pending') ?>" class="small text-white stretched-link">View Details</a>
                        <i class="bi bi-chevron-right text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Orders</h5>
                    <a href="<?= site_url('cashier/orders') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#ORD-001</td>
                                <td>John Doe</td>
                                <td>3</td>
                                <td>$45.99</td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-printer"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-002</td>
                                <td>Jane Smith</td>
                                <td>2</td>
                                <td>$32.50</td>
                                <td><span class="badge bg-warning text-dark">Pending</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-003</td>
                                <td>Robert Johnson</td>
                                <td>5</td>
                                <td>$78.25</td>
                                <td><span class="badge bg-info">Processing</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-004</td>
                                <td>Maria Garcia</td>
                                <td>1</td>
                                <td>$15.75</td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-printer"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Floating Chat Toggle Button -->
<button id="openChatBtn" class="btn btn-primary rounded-circle shadow" 
        style="position: fixed; bottom: 30px; right: 30px; z-index: 1100; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
    <i class="bi bi-chat-dots fs-3"></i>
</button>

<!-- Togglable Chat Panel -->
<div id="chatPanel" class="card shadow" 
     style="position: fixed; bottom: 100px; right: 30px; width: 350px; z-index: 1101; display: none;">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="cursor: pointer;">
        <span><i class="bi bi-chat-dots"></i> Chat</span>
        <button class="btn btn-sm btn-light" id="closeChatBtn" type="button">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="card-body p-0" style="height: 400px;">
        <iframe src="<?= base_url('Realtime-chat-application-main/users.php?user=' . session()->get('username') . '&role=' . session()->get('role')) ?>" style="width:100%; height:100%; border:none;"></iframe>
    </div>
</div>

<script>
const openChatBtn = document.getElementById('openChatBtn');
const chatPanel = document.getElementById('chatPanel');
const closeChatBtn = document.getElementById('closeChatBtn');

openChatBtn.addEventListener('click', function() {
    chatPanel.style.display = 'block';
    openChatBtn.style.display = 'none';
});
closeChatBtn.addEventListener('click', function() {
    chatPanel.style.display = 'none';
    openChatBtn.style.display = 'flex';
});
</script>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-3 mt-auto border-top">
        <div class="container text-center">
            <p class="text-muted mb-0">Order Management System &copy; <?= date('Y') ?></p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>