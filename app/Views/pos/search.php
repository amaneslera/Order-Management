<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Order - POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary: #6B4423; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, #3E2723 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            border-radius: 10px;
            margin: 5px 10px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .search-icon {
            font-size: 3rem;
            color: var(--primary);
        }
        .search-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 24px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0 d-flex flex-column">
                <div class="p-4">
                    <h4><i class="bi bi-shop me-2"></i>POS System</h4>
                    <hr class="border-light">
                </div>
                <nav class="nav flex-column">
                    <a href="<?= base_url('pos') ?>" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a href="<?= base_url('pos/order/new') ?>" class="nav-link"><i class="bi bi-cart-plus me-2"></i>New Counter Order</a>
                    <a href="<?= base_url('staff/send-sms') ?>" class="nav-link"><i class="bi bi-chat-text me-2"></i>Message Admin</a>
                    <a href="<?= base_url('pos/search') ?>" class="nav-link active"><i class="bi bi-search me-2"></i>Search Order</a>
                    <a href="<?= base_url('pos/orders') ?>" class="nav-link"><i class="bi bi-list-ul me-2"></i>All Orders</a>
                    <?php if (session()->get('role') === 'Admin'): ?>
                        <a href="<?= base_url('admin') ?>" class="nav-link"><i class="bi bi-gear me-2"></i>Admin Panel</a>
                    <?php endif; ?>
                    <hr class="border-light">
                    <a href="<?= base_url('barcode-master/scan.php') ?>" class="nav-link" target="_blank"><i class="bi bi-upc-scan me-2"></i>Scan Barcode</a>
                    <hr class="border-light">
                    <a href="<?= base_url('logout') ?>" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </nav>
                <div class="p-4 mt-auto">
                    <small class="text-light">
                        <i class="bi bi-person-circle me-2"></i>
                        <?= esc((session()->get('username') ?? session()->get('name'))) ?><br>
                        <span class="badge bg-light text-dark mt-1"><?= ucfirst(session()->get('role')) ?></span>
                    </small>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><i class="bi bi-search me-2"></i>Search Order</h2>
                        <p class="text-muted mb-0">Enter or scan the order number</p>
                    </div>
                </div>
                <div class="search-card mb-4" style="max-width:600px; margin:0 auto;">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <form action="<?= base_url('pos/search') ?>" method="GET">
                        <div class="mb-4">
                            <label for="order_number" class="form-label h5">Order Number</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="bi bi-upc-scan"></i>
                                </span>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="order_number" 
                                       name="order_number" 
                                       placeholder="A001" 
                                       autofocus 
                                       required>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                You can manually type or scan the barcode
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="bi bi-search me-2"></i>Search Order
                        </button>
                        <div class="text-center">
                            <a href="<?= base_url('pos') ?>" class="text-decoration-none">
                                <i class="bi bi-house me-1"></i>Back to Dashboard
                            </a>
                        </div>
                    </form>
                    <hr class="my-4">
                    <div class="text-center">
                        <h6 class="text-muted mb-3">Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('pos/orders?status=pending') ?>" class="btn btn-outline-warning">
                                <i class="bi bi-clock-history me-2"></i>View Pending Orders
                            </a>
                            <a href="<?= base_url('pos/orders') ?>" class="btn btn-outline-primary">
                                <i class="bi bi-list-ul me-2"></i>View All Orders
                            </a>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <small class="text-muted">
                        Logged in as: <strong><?= esc((session()->get('username') ?? session()->get('name'))) ?></strong>
                        (<?= ucfirst(session()->get('role')) ?>)
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-submit on barcode scan (when Enter is pressed)
        document.getElementById('order_number').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.target.form.submit();
            }
        });

        // Auto-focus on the input field
        window.addEventListener('load', function() {
            document.getElementById('order_number').focus();
        });
    </script>
</body>
</html>
