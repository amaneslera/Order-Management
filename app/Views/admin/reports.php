<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <a href="<?= base_url('admin/reports') ?>" class="nav-link active"><i class="bi bi-graph-up me-2"></i>Reports</a>
                    <a href="<?= base_url('admin/menu') ?>" class="nav-link"><i class="bi bi-cup-hot me-2"></i>Menu Items</a>
                    <a href="<?= base_url('admin/inventory') ?>" class="nav-link"><i class="bi bi-box-seam me-2"></i>Inventory</a>
                    <a href="<?= base_url('admin/users') ?>" class="nav-link"><i class="bi bi-people me-2"></i>Users</a>
                    <a href="<?= base_url('admin/activity-logs') ?>" class="nav-link"><i class="bi bi-activity me-2"></i>Activity Logs</a>
                    <a href="<?= base_url('pos') ?>" class="nav-link"><i class="bi bi-shop me-2"></i>POS System</a>
                    <a href="<?= base_url('barcode-master/scan.php') ?>" class="nav-link"><i class="bi bi-camera me-2"></i>Scan Barcode</a>
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
                <h2><i class="bi bi-graph-up me-2"></i>Sales Reports</h2>
                <p class="text-muted">Analyze your sales performance</p>

                <!-- Report Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="<?= base_url('admin/reports') ?>" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Report Type</label>
                                <select name="type" class="form-select" onchange="this.form.submit()">
                                    <option value="daily" <?= $report_type === 'daily' ? 'selected' : '' ?>>Daily</option>
                                    <option value="weekly" <?= $report_type === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                                    <option value="monthly" <?= $report_type === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                    <option value="custom" <?= $report_type === 'custom' ? 'selected' : '' ?>>Custom Range</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>" required>
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

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="opacity-75">Total Revenue</h6>
                                <h2>₱<?= number_format($total_sales, 2) ?></h2>
                                <small><?= date('M d', strtotime($start_date)) ?> - <?= date('M d, Y', strtotime($end_date)) ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="opacity-75">Total Orders</h6>
                                <h2><?= number_format($total_orders) ?></h2>
                                <small>Orders completed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="opacity-75">Average Order Value</h6>
                                <h2>₱<?= $total_orders > 0 ? number_format($total_sales / $total_orders, 2) : '0.00' ?></h2>
                                <small>Per order</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Chart -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>Sales Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" height="80"></canvas>
                    </div>
                </div>

                <div class="row">
                    <!-- Top Selling Items -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Top Selling Items</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Item</th>
                                                <th class="text-end">Sold</th>
                                                <th class="text-end">Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($top_selling as $index => $item): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td>
                                                    <strong><?= esc($item['name']) ?></strong>
                                                    <br><small class="text-muted"><?= esc($item['category']) ?></small>
                                                </td>
                                                <td class="text-end"><?= $item['total_quantity'] ?></td>
                                                <td class="text-end text-success"><strong>₱<?= number_format($item['total_revenue'], 2) ?></strong></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Payment Methods</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="paymentChart" height="200"></canvas>
                                <div class="mt-3">
                                    <?php foreach ($payment_methods as $method): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><i class="bi bi-circle-fill me-2" style="font-size: 8px;"></i><?= ucfirst($method['payment_method']) ?></span>
                                        <strong>₱<?= number_format($method['total'], 2) ?> (<?= $method['count'] ?>)</strong>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Sales Table -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-table me-2"></i>Daily Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-end">Orders</th>
                                        <th class="text-end">Sales</th>
                                        <th class="text-end">Avg Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sales_report as $row): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($row['date'])) ?></td>
                                        <td class="text-end"><?= $row['total_orders'] ?></td>
                                        <td class="text-end text-success"><strong>₱<?= number_format($row['total_sales'], 2) ?></strong></td>
                                        <td class="text-end">₱<?= number_format($row['total_sales'] / $row['total_orders'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Chat Button (integrated with your existing chat) -->
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
        // Chat toggle
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

        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($sales_report, 'date')) ?>,
                datasets: [{
                    label: 'Sales (₱)',
                    data: <?= json_encode(array_column($sales_report, 'total_sales')) ?>,
                    borderColor: '#6B4423',
                    backgroundColor: 'rgba(107, 68, 35, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });

        // Payment Methods Chart
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($payment_methods, 'payment_method')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($payment_methods, 'total')) ?>,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d']
                }]
            }
        });
    </script>
</body>
</html>
