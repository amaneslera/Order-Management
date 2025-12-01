<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report - Coffee Kiosk</title>
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
        .report-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .action-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        @media print {
            .sidebar, .no-print { display: none !important; }
            .col-md-10 { width: 100% !important; max-width: 100% !important; }
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
                    <a href="<?= base_url('admin/menu') ?>" class="nav-link"><i class="bi bi-cup-hot me-2"></i>Menu Items</a>
                    <a href="<?= base_url('admin/inventory') ?>" class="nav-link"><i class="bi bi-box-seam me-2"></i>Inventory</a>
                    <a href="<?= base_url('admin/users') ?>" class="nav-link"><i class="bi bi-people me-2"></i>Users</a>
                    <a href="<?= base_url('admin/activity-logs') ?>" class="nav-link"><i class="bi bi-activity me-2"></i>Activity Logs</a>
                    <a href="<?= base_url('pos') ?>" class="nav-link"><i class="bi bi-shop me-2"></i>POS System</a>
                    <hr class="border-light">
                    <a href="<?= base_url('kiosk') ?>" class="nav-link" target="_blank"><i class="bi bi-phone me-2"></i>View Kiosk</a>
                    <hr class="border-light">
                    <a href="<?= base_url('logout') ?>" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </nav>
                <div class="p-4 mt-auto">
                    <small class="text-light">
                        <i class="bi bi-person-circle me-2"></i>
                        <?= esc(session()->get('name')) ?><br>
                        <span class="badge bg-danger mt-1"><?= ucfirst(session()->get('role')) ?></span>
                    </small>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                    <div>
                        <h2><i class="bi bi-clipboard-data me-2"></i>Inventory Activity Report</h2>
                        <p class="text-muted">Track all stock changes and movements</p>
                    </div>
                    <div>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="bi bi-printer me-2"></i>Print Report
                        </button>
                        <a href="<?= base_url('admin/inventory') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Inventory
                        </a>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="card report-card mb-4 no-print">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Report</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?= base_url('admin/inventory/report') ?>" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="<?= $start_date ?? date('Y-m-d', strtotime('-7 days')) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="<?= $end_date ?? date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Action Type</label>
                                <select name="action" class="form-select">
                                    <option value="">All Actions</option>
                                    <option value="add" <?= ($action ?? '') === 'add' ? 'selected' : '' ?>>Stock Added</option>
                                    <option value="set" <?= ($action ?? '') === 'set' ? 'selected' : '' ?>>Stock Set</option>
                                    <option value="deduct" <?= ($action ?? '') === 'deduct' ? 'selected' : '' ?>>Stock Deducted</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-2"></i>Generate Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Summary -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card report-card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Report Period: <?= isset($start_date) ? date('M d, Y', strtotime($start_date)) : date('M d, Y', strtotime('-7 days')) ?> - <?= isset($end_date) ? date('M d, Y', strtotime($end_date)) : date('M d, Y') ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <h3 class="text-success"><?= $summary['total_added'] ?? 0 ?></h3>
                                        <p class="text-muted">Items Added to Stock</p>
                                    </div>
                                    <div class="col-md-4">
                                        <h3 class="text-danger"><?= $summary['total_deducted'] ?? 0 ?></h3>
                                        <p class="text-muted">Items Sold (Deducted)</p>
                                    </div>
                                    <div class="col-md-4">
                                        <h3 class="text-primary"><?= $summary['total_transactions'] ?? 0 ?></h3>
                                        <p class="text-muted">Total Transactions</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Activity Log -->
                <div class="card report-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Activity Details</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($logs)): ?>
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle me-2"></i>
                                No inventory activity found for the selected period.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Item</th>
                                            <th>Action</th>
                                            <th class="text-center">Quantity Change</th>
                                            <th class="text-center">Previous Stock</th>
                                            <th class="text-center">New Stock</th>
                                            <th>User</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td>
                                                <small>
                                                    <?= date('M d, Y', strtotime($log['created_at'])) ?><br>
                                                    <span class="text-muted"><?= date('h:i A', strtotime($log['created_at'])) ?></span>
                                                </small>
                                            </td>
                                            <td><strong><?= esc($log['item_name']) ?></strong></td>
                                            <td>
                                                <?php if ($log['action'] === 'add'): ?>
                                                    <span class="badge bg-success action-badge">
                                                        <i class="bi bi-plus-circle me-1"></i>Added
                                                    </span>
                                                <?php elseif ($log['action'] === 'deduct'): ?>
                                                    <span class="badge bg-danger action-badge">
                                                        <i class="bi bi-dash-circle me-1"></i>Deducted
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary action-badge">
                                                        <i class="bi bi-pencil me-1"></i>Set
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <strong class="text-<?= $log['action'] === 'add' ? 'success' : ($log['action'] === 'deduct' ? 'danger' : 'primary') ?>">
                                                    <?= $log['action'] === 'add' ? '+' : '' ?><?= $log['quantity_change'] ?>
                                                </strong>
                                            </td>
                                            <td class="text-center"><?= $log['previous_stock'] ?></td>
                                            <td class="text-center"><strong><?= $log['new_stock'] ?></strong></td>
                                            <td>
                                                <small>
                                                    <?= esc($log['user_name'] ?? 'System') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= esc($log['notes'] ?? '-') ?>
                                                    <?php if ($log['order_id']): ?>
                                                        <br><span class="badge bg-info">Order #<?= $log['order_id'] ?></span>
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if (isset($pager)): ?>
                            <div class="d-flex justify-content-center mt-3 no-print">
                                <?= $pager->links() ?>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
