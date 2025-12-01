<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - Admin</title>
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
        .log-item {
            border-left: 3px solid;
            padding-left: 15px;
        }
        .log-login { border-color: #28a745; }
        .log-logout { border-color: #dc3545; }
        .log-add { border-color: #0d6efd; }
        .log-edit { border-color: #ffc107; }
        .log-delete { border-color: #6c757d; }
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
                    <a href="<?= base_url('admin/activity-logs') ?>" class="nav-link active"><i class="bi bi-activity me-2"></i>Activity Logs</a>
                    <a href="<?= base_url('pos') ?>" class="nav-link"><i class="bi bi-shop me-2"></i>POS System</a>
                    <a href="<?= base_url('barcode-master/scan.php') ?>" class="nav-link"><i class="bi bi-camera me-2"></i>Scan Barcode</a>
                    <hr class="border-light">
                    <a href="<?= base_url('kiosk') ?>" class="nav-link" target="_blank"><i class="bi bi-phone me-2"></i>View Kiosk</a>
                    <hr class="border-light">
                    <a href="<?= base_url('logout') ?>" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><i class="bi bi-activity me-2"></i>Activity Logs</h2>
                        <p class="text-muted">System activity and user actions tracking</p>
                    </div>
                    <div>
                        <button class="btn btn-outline-secondary" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                        </button>
                    </div>
                </div>

                <!-- Filter Options -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Action Type</label>
                                <select name="action" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Actions</option>
                                    <option value="login">Login</option>
                                    <option value="logout">Logout</option>
                                    <option value="add_menu_item">Add Menu Item</option>
                                    <option value="edit_menu_item">Edit Menu Item</option>
                                    <option value="delete_menu_item">Delete Menu Item</option>
                                    <option value="add_user">Add User</option>
                                    <option value="edit_user">Edit User</option>
                                    <option value="delete_user">Delete User</option>
                                    <option value="process_payment">Process Payment</option>
                                    <option value="update_order_status">Update Order</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">User Role</label>
                                <select name="role" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="cashier">Cashier</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control">
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($logs)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                                <p class="text-muted mt-3">No activity logs found</p>
                            </div>
                        <?php else: ?>
                            <div class="timeline">
                                <?php foreach ($logs as $log): ?>
                                    <?php
                                    $logClass = 'log-item ';
                                    switch ($log['action']) {
                                        case 'login':
                                            $logClass .= 'log-login';
                                            $icon = 'box-arrow-in-right';
                                            $iconColor = 'text-success';
                                            break;
                                        case 'logout':
                                            $logClass .= 'log-logout';
                                            $icon = 'box-arrow-right';
                                            $iconColor = 'text-danger';
                                            break;
                                        case 'add_menu_item':
                                        case 'add_user':
                                        case 'add_order_item':
                                            $logClass .= 'log-add';
                                            $icon = 'plus-circle';
                                            $iconColor = 'text-primary';
                                            break;
                                        case 'edit_menu_item':
                                        case 'edit_user':
                                        case 'update_order_status':
                                            $logClass .= 'log-edit';
                                            $icon = 'pencil';
                                            $iconColor = 'text-warning';
                                            break;
                                        case 'delete_menu_item':
                                        case 'delete_user':
                                        case 'remove_order_item':
                                            $logClass .= 'log-delete';
                                            $icon = 'trash';
                                            $iconColor = 'text-secondary';
                                            break;
                                        case 'process_payment':
                                            $logClass .= 'log-add';
                                            $icon = 'cash-coin';
                                            $iconColor = 'text-success';
                                            break;
                                        default:
                                            $icon = 'circle';
                                            $iconColor = 'text-info';
                                    }
                                    ?>
                                    <div class="<?= $logClass ?> mb-3 pb-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <i class="bi bi-<?= $icon ?> <?= $iconColor ?> me-2"></i>
                                                    <strong><?= esc($log['action']) ?></strong>
                                                    <span class="badge bg-secondary ms-2"><?= ucfirst($log['role'] ?? 'N/A') ?></span>
                                                </div>
                                                <div class="text-muted small mb-1">
                                                    <i class="bi bi-person me-1"></i>
                                                    <?= esc($log['user_name'] ?? 'Unknown User') ?>
                                                </div>
                                                <?php if (!empty($log['description'])): ?>
                                                    <div class="text-secondary small">
                                                        <?= esc($log['description']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-end text-muted small" style="min-width: 120px;">
                                                <i class="bi bi-clock me-1"></i>
                                                <?= date('M d, Y', strtotime($log['created_at'])) ?><br>
                                                <?= date('h:i A', strtotime($log['created_at'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Pagination (if needed) -->
                            <div class="text-center mt-4">
                                <small class="text-muted">Showing latest 100 activities</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Integration -->
    <button id="openChatBtn" class="btn btn-primary rounded-circle shadow" 
            style="position: fixed; bottom: 30px; right: 30px; z-index: 1100; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-chat-dots fs-3"></i>
    </button>

    <div id="chatPanel" class="card shadow" 
         style="position: fixed; bottom: 100px; right: 30px; width: 350px; z-index: 1101; display: none;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-chat-dots"></i> Chat</span>
            <button class="btn btn-sm btn-light" id="closeChatBtn" type="button">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="card-body p-0" style="height: 400px;">
            <iframe src="<?= base_url('Realtime-chat-application-main/users.php?user=' . session()->get('name') . '&role=' . session()->get('role')) ?>" 
                    style="width:100%; height:100%; border:none;"></iframe>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const openChatBtn = document.getElementById('openChatBtn');
        const chatPanel = document.getElementById('chatPanel');
        const closeChatBtn = document.getElementById('closeChatBtn');

        openChatBtn.addEventListener('click', () => {
            chatPanel.style.display = 'block';
            openChatBtn.style.display = 'none';
        });
        closeChatBtn.addEventListener('click', () => {
            chatPanel.style.display = 'none';
            openChatBtn.style.display = 'flex';
        });
    </script>
</body>
</html>
