<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Coffee Kiosk</title>
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
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
                    <a href="<?= base_url('admin') ?>" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a href="<?= base_url('admin/reports') ?>" class="nav-link"><i class="bi bi-graph-up me-2"></i>Reports</a>
                    <a href="<?= base_url('admin/menu') ?>" class="nav-link"><i class="bi bi-cup-hot me-2"></i>Menu Items</a>
                    <a href="<?= base_url('admin/users') ?>" class="nav-link"><i class="bi bi-people me-2"></i>Users</a>
                    <a href="<?= base_url('admin/activity-logs') ?>" class="nav-link"><i class="bi bi-activity me-2"></i>Activity Logs</a>
                    <a href="<?= base_url('pos') ?>" class="nav-link"><i class="bi bi-shop me-2"></i>POS System</a>
                    <hr class="border-light">
                    <a href="<?= base_url('barcode-master/dashboard.php') ?>" class="nav-link" target="_blank"><i class="bi bi-upc-scan me-2"></i>Barcode System</a>
                    <a href="#" id="openChatLink" class="nav-link"><i class="bi bi-chat-dots me-2"></i>Messages</a>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2>Admin Dashboard</h2>
                        <p class="text-muted">Coffee Kiosk Management System</p>
                    </div>
                    <div>
                        <span class="text-muted"><i class="bi bi-calendar3 me-2"></i><?= date('F d, Y') ?></span>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 opacity-75">Today's Orders</h6>
                                        <h2 class="mb-0"><?= $total_orders_today ?></h2>
                                    </div>
                                    <i class="bi bi-receipt-cutoff" style="font-size: 3rem; opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 opacity-75">Today's Revenue</h6>
                                        <h2 class="mb-0">₱<?= number_format($total_revenue_today, 2) ?></h2>
                                    </div>
                                    <i class="bi bi-currency-dollar" style="font-size: 3rem; opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 opacity-75">Pending Orders</h6>
                                        <h2 class="mb-0"><?= $pending_orders ?></h2>
                                    </div>
                                    <i class="bi bi-hourglass-split" style="font-size: 3rem; opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 opacity-75">Avg Order Value</h6>
                                        <h2 class="mb-0">₱<?= $total_orders_today > 0 ? number_format($total_revenue_today / $total_orders_today, 2) : '0.00' ?></h2>
                                    </div>
                                    <i class="bi bi-graph-up-arrow" style="font-size: 3rem; opacity: 0.5;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Selling Items -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Top Selling Items (Last 30 Days)</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($top_selling)): ?>
                                    <p class="text-muted text-center py-3">No sales data available</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Item</th>
                                                    <th>Category</th>
                                                    <th class="text-end">Quantity Sold</th>
                                                    <th class="text-end">Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($top_selling as $index => $item): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td>
                                                        <strong><?= esc($item['name']) ?></strong>
                                                    </td>
                                                    <td><span class="badge bg-secondary"><?= esc($item['category']) ?></span></td>
                                                    <td class="text-end"><?= $item['total_quantity'] ?></td>
                                                    <td class="text-end text-success"><strong>₱<?= number_format($item['total_revenue'], 2) ?></strong></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Orders</h5>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <?php if (empty($recent_orders)): ?>
                                    <p class="text-muted text-center">No recent orders</p>
                                <?php else: ?>
                                    <?php foreach ($recent_orders as $order): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                        <div>
                                            <div class="fw-bold small"><?= esc($order['order_number']) ?></div>
                                            <small class="text-muted"><?= date('h:i A', strtotime($order['created_at'])) ?></small>
                                        </div>
                                        <div class="text-end">
                                            <div class="text-primary fw-bold">₱<?= number_format($order['total_amount'], 2) ?></div>
                                            <span class="badge bg-<?= $order['status'] === 'pending' ? 'warning' : ($order['status'] === 'paid' ? 'success' : 'info') ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <a href="<?= base_url('admin/menu/add') ?>" class="btn btn-outline-primary w-100 mb-2">
                                            <i class="bi bi-plus-circle me-2"></i>Add Menu Item
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?= base_url('admin/reports') ?>" class="btn btn-outline-success w-100 mb-2">
                                            <i class="bi bi-file-earmark-bar-graph me-2"></i>View Reports
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?= base_url('admin/users/add') ?>" class="btn btn-outline-info w-100 mb-2">
                                            <i class="bi bi-person-plus me-2"></i>Add User
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-outline-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#emailReportModal">
                                            <i class="bi bi-envelope me-2"></i>Send Daily Report
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
         style="position: fixed; bottom: 100px; right: 30px; width: 400px; z-index: 1101; display: none;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-chat-dots me-2"></i>Chat</span>
            <button class="btn btn-sm btn-light" id="closeChatBtn" type="button">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="card-body p-0" style="height: 500px;">
            <iframe src="<?= base_url('Realtime-chat-application-main/users.php?user=' . session()->get('username') . '&role=' . session()->get('role')) ?>" 
                    style="width:100%; height:100%; border:none;"></iframe>
        </div>
    </div>

    <!-- Email Report Modal -->
    <div class="modal fade" id="emailReportModal" tabindex="-1" aria-labelledby="emailReportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="emailReportModalLabel">
                        <i class="bi bi-envelope me-2"></i>Send Daily Sales Report
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="emailReportForm">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            This will send today's sales report including total revenue, order statistics, top selling items, and payment methods summary.
                        </div>
                        <div class="mb-3">
                            <label for="recipientEmail" class="form-label">Recipient Email Address</label>
                            <input type="email" class="form-control" id="recipientEmail" name="email" 
                                   placeholder="admin@example.com" required>
                            <div class="form-text">Enter the email address to receive the daily report</div>
                        </div>
                        <div class="alert alert-warning">
                            <strong>Note:</strong> Make sure you have configured your email settings in the <code>.env</code> file. 
                            You need a Gmail App Password (not your regular password).
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="sendReportBtn">
                        <i class="bi bi-send me-2"></i>Send Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const openChatBtn = document.getElementById('openChatBtn');
        const openChatLink = document.getElementById('openChatLink');
        const chatPanel = document.getElementById('chatPanel');
        const closeChatBtn = document.getElementById('closeChatBtn');

        function openChat() {
            chatPanel.style.display = 'block';
            openChatBtn.style.display = 'none';
        }

        function closeChat() {
            chatPanel.style.display = 'none';
            openChatBtn.style.display = 'flex';
        }

        openChatBtn.addEventListener('click', openChat);
        openChatLink.addEventListener('click', function(e) {
            e.preventDefault();
            openChat();
        });
        closeChatBtn.addEventListener('click', closeChat);

        // Email Report Functionality
        const sendReportBtn = document.getElementById('sendReportBtn');
        const emailReportForm = document.getElementById('emailReportForm');
        const emailReportModal = new bootstrap.Modal(document.getElementById('emailReportModal'));

        sendReportBtn.addEventListener('click', async function() {
            const email = document.getElementById('recipientEmail').value;
            
            if (!email) {
                alert('Please enter a recipient email address');
                return;
            }

            // Disable button and show loading
            sendReportBtn.disabled = true;
            sendReportBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

            try {
                const formData = new FormData();
                formData.append('email', email);
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                const response = await fetch('<?= base_url('admin/send-daily-report') ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('✅ Daily sales report has been sent successfully to ' + email);
                    emailReportModal.hide();
                    emailReportForm.reset();
                } else {
                    alert('❌ Failed to send report: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error sending report:', error);
                alert('❌ Error sending report. Please check your email configuration in .env file and try again.');
            } finally {
                // Re-enable button
                sendReportBtn.disabled = false;
                sendReportBtn.innerHTML = '<i class="bi bi-send me-2"></i>Send Report';
            }
        });
    </script>
</body>
</html>
