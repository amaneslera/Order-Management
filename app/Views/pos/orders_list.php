<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders - POS</title>
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
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
        }
        .badge {
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4">
                    <h4><i class="bi bi-shop me-2"></i>POS System</h4>
                    <hr class="border-light">
                </div>
                <nav class="nav flex-column">
                    <a href="<?= base_url('pos') ?>" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a href="<?= base_url('kiosk') ?>" class="nav-link" target="_blank"><i class="bi bi-cart-plus me-2"></i>Walk-in Customer Order</a>
                    <a href="<?= base_url('staff/send-sms') ?>" class="nav-link"><i class="bi bi-chat-text me-2"></i>Message Admin</a>
                    <a href="<?= base_url('pos/search') ?>" class="nav-link"><i class="bi bi-search me-2"></i>Search Order</a>
                    <a href="<?= base_url('pos/orders') ?>" class="nav-link active"><i class="bi bi-list-ul me-2"></i>All Orders</a>
                    <?php if (session()->get('role') === 'Admin'): ?>
                        <a href="<?= base_url('admin') ?>" class="nav-link"><i class="bi bi-gear me-2"></i>Admin Panel</a>
                    <?php endif; ?>
                    <hr class="border-light">
                    <a href="<?= base_url('barcode-master/scan.php') ?>" class="nav-link" target="_blank"><i class="bi bi-upc-scan me-2"></i>Scan Barcode</a>
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
                        <h2><i class="bi bi-list-ul me-2"></i>All Orders</h2>
                        <p class="text-muted">Manage and track all orders</p>
                    </div>
                    <div class="text-muted">
                        <span class="badge bg-info"><?= count($orders) ?> orders</span>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="btn-group" role="group">
                            <a href="<?= base_url('pos/orders') ?>" class="btn btn-sm <?= !$selected_status ? 'btn-primary' : 'btn-outline-primary' ?>">
                                <i class="bi bi-funnel me-1"></i>All
                            </a>
                            <a href="<?= base_url('pos/orders?status=pending') ?>" class="btn btn-sm <?= $selected_status === 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>">
                                <i class="bi bi-clock me-1"></i>Pending
                            </a>
                            <a href="<?= base_url('pos/orders?status=completed') ?>" class="btn btn-sm <?= $selected_status === 'completed' ? 'btn-success' : 'btn-outline-success' ?>">
                                <i class="bi bi-check-circle me-1"></i>Completed
                            </a>
                            <a href="<?= base_url('pos/orders?status=paid') ?>" class="btn btn-sm <?= $selected_status === 'paid' ? 'btn-info' : 'btn-outline-info' ?>">
                                <i class="bi bi-credit-card me-1"></i>Paid
                            </a>
                            <a href="<?= base_url('pos/orders?status=cancelled') ?>" class="btn btn-sm <?= $selected_status === 'cancelled' ? 'btn-danger' : 'btn-outline-danger' ?>">
                                <i class="bi bi-x-circle me-1"></i>Cancelled
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-3">No orders found<?= $selected_status ? " with status <strong>" . ucfirst($selected_status) . "</strong>" : "" ?>.</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <strong>#<?= $order['id'] ?></strong>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?>
                                            <?php if (!empty($order['customer_email'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($order['customer_email']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= $order['total_items'] ?? 0 ?> items</span>
                                        </td>
                                        <td>
                                            <strong>₱<?= number_format($order['total_amount'] ?? 0, 2) ?></strong>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = match($order['status']) {
                                                'pending' => 'warning',
                                                'completed' => 'success',
                                                'paid' => 'info',
                                                'cancelled' => 'danger',
                                                default => 'secondary'
                                            };
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('M d, Y H:i', strtotime($order['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= base_url('pos/order/' . $order['id']) ?>" class="btn btn-outline-primary" title="View Order">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if ($order['status'] === 'pending'): ?>
                                                <a href="<?= base_url('pos/complete/' . $order['id']) ?>" class="btn btn-outline-success" title="Mark Complete" onclick="return confirm('Mark this order as complete?')">
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if ($order['status'] !== 'paid' && $order['status'] !== 'cancelled'): ?>
                                                <a href="<?= base_url('pos/payment/' . $order['id']) ?>" class="btn btn-outline-info" title="Process Payment">
                                                    <i class="bi bi-credit-card"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
