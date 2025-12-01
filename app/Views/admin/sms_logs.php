<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff SMS Logs - Admin Panel</title>
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
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .badge-sent { background: #28a745; }
        .badge-failed { background: #dc3545; }
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
                    <a href="<?= base_url('admin/users') ?>" class="nav-link"><i class="bi bi-people me-2"></i>Users</a>
                    <a href="<?= base_url('admin/sms-logs') ?>" class="nav-link active"><i class="bi bi-chat-text me-2"></i>SMS Logs</a>
                    <a href="<?= base_url('admin/activity-logs') ?>" class="nav-link"><i class="bi bi-activity me-2"></i>Activity Logs</a>
                    <hr class="border-light">
                    <a href="<?= base_url('logout') ?>" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><i class="bi bi-chat-text me-2"></i>Staff SMS Logs</h2>
                        <p class="text-muted">Messages received from staff members</p>
                    </div>
                    <div>
                        <a href="<?= base_url('admin') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card text-white bg-success">
                            <div class="card-body">
                                <h6 class="text-uppercase">Total Sent</h6>
                                <h2><?= number_format($statistics['total_sent']) ?></h2>
                                <small><i class="bi bi-check-circle me-1"></i>Successfully delivered</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-white bg-danger">
                            <div class="card-body">
                                <h6 class="text-uppercase">Failed</h6>
                                <h2><?= number_format($statistics['total_failed']) ?></h2>
                                <small><i class="bi bi-x-circle me-1"></i>Delivery failed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-white bg-primary">
                            <div class="card-body">
                                <h6 class="text-uppercase">Today</h6>
                                <h2><?= number_format($statistics['today_count']) ?></h2>
                                <small><i class="bi bi-calendar-check me-1"></i>Messages today</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-white bg-info">
                            <div class="card-body">
                                <h6 class="text-uppercase">Success Rate</h6>
                                <h2><?= $statistics['success_rate'] ?>%</h2>
                                <small><i class="bi bi-graph-up me-1"></i>Overall success</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SMS Messages Table -->
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-list me-2"></i>All Messages</h5>
                            <div>
                                <button class="btn btn-sm btn-outline-primary" onclick="filterLogs('all')">All</button>
                                <button class="btn btn-sm btn-outline-success" onclick="filterLogs('SENT')">Sent</button>
                                <button class="btn btn-sm btn-outline-danger" onclick="filterLogs('FAILED')">Failed</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($sms_logs)): ?>
                            <p class="text-muted text-center py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                No SMS messages received yet
                            </p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="smsTable">
                                    <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>From Staff</th>
                                            <th>Message</th>
                                            <th>Status</th>
                                            <th>Phone</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sms_logs as $log): ?>
                                        <tr data-status="<?= $log['status'] ?>">
                                            <td>
                                                <small>
                                                    <i class="bi bi-calendar me-1"></i>
                                                    <?= date('M d, Y', strtotime($log['created_at'])) ?><br>
                                                    <i class="bi bi-clock me-1"></i>
                                                    <?= date('h:i A', strtotime($log['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <strong><?= esc($log['staff_name']) ?></strong><br>
                                                <small class="text-muted">
                                                    <?= isset($log['role']) ? ucfirst($log['role']) : 'Staff' ?>
                                                </small>
                                            </td>
                                            <td><?= esc($log['message']) ?></td>
                                            <td>
                                                <?php if ($log['status'] === 'SENT'): ?>
                                                    <span class="badge badge-sent">
                                                        <i class="bi bi-check-circle me-1"></i>SENT
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-failed">
                                                        <i class="bi bi-x-circle me-1"></i>FAILED
                                                    </span>
                                                <?php endif; ?>
                                                <?php if ($log['status'] === 'SENT' && !empty($log['sent_at'])): ?>
                                                    <br><small class="text-muted">
                                                        <?= date('h:i A', strtotime($log['sent_at'])) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?= esc($log['admin_phone']) ?></small>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="viewDetails(<?= htmlspecialchars(json_encode($log)) ?>)">
                                                    <i class="bi bi-eye"></i> Details
                                                </button>
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

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>SMS Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Content loaded via JS -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterLogs(status) {
            const rows = document.querySelectorAll('#smsTable tbody tr');
            rows.forEach(row => {
                if (status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function viewDetails(log) {
            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            const modalBody = document.getElementById('modalBody');
            
            let html = `
                <div class="mb-3">
                    <strong>From:</strong> ${log.staff_name}<br>
                    <strong>Date:</strong> ${new Date(log.created_at).toLocaleString()}<br>
                    <strong>Status:</strong> <span class="badge ${log.status === 'SENT' ? 'badge-sent' : 'badge-failed'}">${log.status}</span>
                </div>
                <div class="mb-3">
                    <strong>Message:</strong>
                    <div class="p-3 bg-light rounded mt-2">${log.message}</div>
                </div>
                <div class="mb-3">
                    <strong>Admin Phone:</strong> ${log.admin_phone}
                </div>
            `;
            
            if (log.status === 'SENT' && log.sms_id) {
                html += `<div class="mb-3"><strong>SMS ID:</strong> ${log.sms_id}</div>`;
            }
            
            if (log.status === 'FAILED' && log.error_message) {
                html += `
                    <div class="alert alert-danger">
                        <strong>Error:</strong> ${log.error_message}
                    </div>
                `;
            }
            
            modalBody.innerHTML = html;
            modal.show();
        }
    </script>
</body>
</html>
