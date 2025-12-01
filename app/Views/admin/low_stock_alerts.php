<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Alerts - Admin</title>
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4">
                    <h4><i class="bi bi-shield-check me-2"></i>Admin Panel</h4>
                    <hr class="border-light">
                </div>
                <nav class="nav flex-column">
                    <a href="<?= base_url('admin') ?>" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a href="<?= base_url('admin/reports') ?>" class="nav-link"><i class="bi bi-graph-up me-2"></i>Reports</a>
                    <a href="<?= base_url('admin/inventory') ?>" class="nav-link"><i class="bi bi-box-seam me-2"></i>Inventory</a>
                    <a href="<?= base_url('admin/inventory/low-stock') ?>" class="nav-link active"><i class="bi bi-exclamation-triangle me-2"></i>Low Stock</a>
                    <a href="<?= base_url('admin/menu') ?>" class="nav-link"><i class="bi bi-cup-hot me-2"></i>Menu Items</a>
                    <hr class="border-light">
                    <a href="<?= base_url('kiosk') ?>" class="nav-link" target="_blank"><i class="bi bi-phone me-2"></i>View Kiosk</a>
                    <hr class="border-light">
                    <a href="<?= base_url('logout') ?>" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2><i class="bi bi-exclamation-triangle text-warning me-2"></i>Low Stock Alerts</h2>
                <p class="text-muted">Items that need restocking</p>

                <!-- Out of Stock Items -->
                <?php if (!empty($out_of_stock_items)): ?>
                <div class="card border-danger mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-x-circle me-2"></i>Out of Stock (<?= count($out_of_stock_items) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($out_of_stock_items as $item): ?>
                                    <tr>
                                        <td><strong><?= esc($item['name']) ?></strong></td>
                                        <td><?= esc($item['category']) ?></td>
                                        <td>₱<?= number_format($item['price'], 2) ?></td>
                                        <td><span class="badge bg-danger">0</span></td>
                                        <td>
                                            <a href="<?= base_url('admin/inventory') ?>" class="btn btn-sm btn-danger">
                                                <i class="bi bi-plus-circle me-1"></i>Restock Now
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Low Stock Items -->
                <?php if (!empty($low_stock_items)): ?>
                <div class="card border-warning mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="bi bi-exclamation-circle me-2"></i>Low Stock (<?= count($low_stock_items) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Current Stock</th>
                                        <th>Threshold</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($low_stock_items as $item): ?>
                                    <tr>
                                        <td><strong><?= esc($item['name']) ?></strong></td>
                                        <td><?= esc($item['category']) ?></td>
                                        <td>₱<?= number_format($item['price'], 2) ?></td>
                                        <td><span class="badge bg-warning"><?= $item['stock_quantity'] ?></span></td>
                                        <td><?= $item['low_stock_threshold'] ?></td>
                                        <td>
                                            <a href="<?= base_url('admin/inventory') ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-plus-circle me-1"></i>Restock
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (empty($out_of_stock_items) && empty($low_stock_items)): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Great!</strong> All items have sufficient stock.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
