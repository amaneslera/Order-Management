<?php
// Alerts dashboard view
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Alerts - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary: #6B4423; --danger: #dc3545; --warning: #ffc107; }
        body { background: #f8f9fa; }
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
        .alert-card { border-left: 5px solid var(--danger); margin-bottom: 15px; }
        .alert-card.low-stock { border-left-color: var(--warning); }
        .badge-out-of-stock { background: var(--danger); }
        .badge-low-stock { background: var(--warning); color: #000; }
        .stat-card { 
            border-radius: 10px; 
            padding: 20px; 
            color: white; 
            text-align: center;
            margin-bottom: 15px;
        }
        .stat-card.total { background: var(--primary); }
        .stat-card.low-stock { background: var(--warning); color: #000; }
        .stat-card.out-stock { background: var(--danger); }
        .stat-number { font-size: 28px; font-weight: bold; }
        .item-stock { font-size: 12px; opacity: 0.8; }
        table { font-size: 14px; }
        .dismiss-btn { cursor: pointer; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar p-0">
                <div class="p-4">
                    <h4><i class="bi bi-shield-check me-2"></i>Admin Panel</h4>
                    <hr class="border-light">
                </div>
                <nav class="nav flex-column">
                    <a href="<?= base_url('admin') ?>" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a href="<?= base_url('admin/reports') ?>" class="nav-link"><i class="bi bi-graph-up me-2"></i>Reports</a>
                    <a href="<?= base_url('admin/menu') ?>" class="nav-link"><i class="bi bi-cup-hot me-2"></i>Menu Items</a>
                    <a href="<?= base_url('admin/menu/inventory') ?>" class="nav-link"><i class="bi bi-box-seam me-2"></i>Inventory</a>
                    <a href="<?= base_url('admin/menu/alerts') ?>" class="nav-link active"><i class="bi bi-exclamation-triangle me-2"></i>Stock Alerts</a>
                    <a href="<?= base_url('admin/users') ?>" class="nav-link"><i class="bi bi-people me-2"></i>Users</a>
                    <a href="<?= base_url('admin/sms-logs') ?>" class="nav-link"><i class="bi bi-chat-text me-2"></i>SMS Logs</a>
                    <a href="<?= base_url('admin/activity-logs') ?>" class="nav-link"><i class="bi bi-activity me-2"></i>Activity Logs</a>
                    <a href="<?= base_url('pos') ?>" class="nav-link"><i class="bi bi-shop me-2"></i>Open Cashier POS</a>
                    <hr class="border-light">
                    <a href="<?= base_url('kiosk') ?>" class="nav-link" target="_blank"><i class="bi bi-phone me-2"></i>View Kiosk</a>
                    <a href="<?= base_url('barcode-master/scan.php') ?>" class="nav-link" target="_blank"><i class="bi bi-upc-scan me-2"></i>Barcode Scanner</a>
                    <a href="<?= base_url('Realtime-chat-application-main/users.php?user=' . session()->get('name') . '&role=' . session()->get('role')) ?>" class="nav-link" target="_blank"><i class="bi bi-chat-dots me-2"></i>Messages</a>
                    <hr class="border-light">
                    <a href="<?= base_url('logout') ?>" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </nav>
            </div>

            <div class="col-md-10 p-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="bi bi-exclamation-triangle me-2"></i>Stock Alerts</h2>
                <p class="text-muted">Monitor low stock and out-of-stock items</p>
            </div>
            <div>
                <a href="<?= base_url('admin/menu/inventory') ?>" class="btn btn-outline-primary">
                    <i class="bi bi-box-seam me-2"></i>Inventory
                </a>
                <button class="btn btn-primary ms-2" onclick="checkStockLevels()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Check Now
                </button>
            </div>
        </div>

        <!-- Alert Statistics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card total">
                    <div class="item-stock">Items Needing Attention</div>
                    <div class="stat-number"><?= $alert_stats['total_today'] ?? 0 ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card low-stock">
                    <div class="item-stock">Low Stock</div>
                    <div class="stat-number"><?= $alert_stats['low_stock'] ?? 0 ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card out-stock">
                    <div class="item-stock">Out of Stock</div>
                    <div class="stat-number"><?= $alert_stats['out_of_stock'] ?? 0 ?></div>
                </div>
            </div>
        </div>

        <!-- Tabs for different views -->
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-alerts-tab" data-bs-toggle="tab" data-bs-target="#active-alerts" type="button">
                    <i class="bi bi-bell me-2"></i>Active Alerts
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="low-stock-tab" data-bs-toggle="tab" data-bs-target="#low-stock" type="button">
                    <i class="bi bi-graph-down me-2"></i>Low Stock Items
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="out-stock-tab" data-bs-toggle="tab" data-bs-target="#out-stock" type="button">
                    <i class="bi bi-exclamation-circle me-2"></i>Out of Stock Items
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Active Alerts -->
            <div class="tab-pane fade show active" id="active-alerts" role="tabpanel">
                <?php if (empty($active_alerts)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>All inventory levels are healthy. No active alerts.
                    </div>
                <?php else: ?>
                    <div class="alert-container">
                        <?php foreach ($active_alerts as $alert): ?>
                            <div class="card alert-card <?= $alert['alert_type'] === 'low_stock' ? 'low-stock' : '' ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="card-title mb-1">
                                                <?= esc($alert['name']) ?>
                                                <span class="badge <?= $alert['alert_type'] === 'low_stock' ? 'badge-low-stock' : 'badge-out-of-stock' ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $alert['alert_type'])) ?>
                                                </span>
                                            </h5>
                                            <p class="card-text mb-0 text-muted">
                                                <small>
                                                    Current Stock: <strong><?= $alert['current_stock'] ?></strong> units 
                                                    | Threshold: <?= $alert['threshold'] ?> units
                                                </small>
                                            </p>
                                            <p class="card-text mb-0 text-muted">
                                                <small><i class="bi bi-clock me-1"></i><?= date('M d, Y \a\t h:i A', strtotime($alert['created_at'])) ?></small>
                                            </p>
                                        </div>
                                        <div>
                                            <span class="badge bg-info me-2">
                                                <i class="bi bi-chat-left-dots me-1"></i><?= $alert['sent_sms'] ? 'SMS Sent' : 'Pending'?>
                                            </span>
                                            <?php if (!empty($alert['is_realtime'])): ?>
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-arrow-repeat me-1"></i>Real-time
                                                </span>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-danger dismiss-btn" onclick="dismissAlert(<?= $alert['id'] ?>)">
                                                    <i class="bi bi-x-lg me-1"></i>Dismiss
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Low Stock Items -->
            <div class="tab-pane fade" id="low-stock" role="tabpanel">
                <?php if (empty($low_stock_items)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>No items with low stock levels.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Low Stock Threshold</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($low_stock_items as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><strong><?= esc($item['name']) ?></strong></td>
                                        <td><span class="badge bg-secondary"><?= esc($item['category']) ?></span></td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                <?= $item['stock_quantity'] ?> units
                                            </span>
                                        </td>
                                        <td><?= $item['low_stock_threshold'] ?> units</td>
                                        <td>
                                            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>Low Stock
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick='openStockAdjustModal(<?= (int) $item['id'] ?>, <?= json_encode($item['name']) ?>, <?= (int) $item['stock_quantity'] ?>)'>
                                                    <i class="bi bi-pencil-square me-1"></i>Edit Stock
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning" onclick='openThresholdModal(<?= (int) $item['id'] ?>, <?= json_encode($item['name']) ?>, <?= (int) $item['low_stock_threshold'] ?>)'>
                                                    <i class="bi bi-sliders me-1"></i>Threshold
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Out of Stock Items -->
            <div class="tab-pane fade" id="out-stock" role="tabpanel">
                <?php if (empty($out_of_stock_items)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>No items are currently out of stock.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Low Stock Threshold</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($out_of_stock_items as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><strong><?= esc($item['name']) ?></strong></td>
                                        <td><span class="badge bg-secondary"><?= esc($item['category']) ?></span></td>
                                        <td><span class="badge bg-danger"><?= (int) $item['stock_quantity'] ?> units</span></td>
                                        <td><?= (int) $item['low_stock_threshold'] ?> units</td>
                                        <td>₱<?= number_format($item['price'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Out of Stock
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick='openStockAdjustModal(<?= (int) $item['id'] ?>, <?= json_encode($item['name']) ?>, <?= (int) $item['stock_quantity'] ?>)'>
                                                    <i class="bi bi-pencil-square me-1"></i>Edit Stock
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning" onclick='openThresholdModal(<?= (int) $item['id'] ?>, <?= json_encode($item['name']) ?>, <?= (int) $item['low_stock_threshold'] ?>)'>
                                                    <i class="bi bi-sliders me-1"></i>Threshold
                                                </button>
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

    <!-- Stock Adjustment Modal -->
    <div class="modal fade" id="stockAdjustModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2"><strong>Item:</strong> <span id="modalItemName"></span></p>
                    <p class="mb-3"><strong>Current Stock:</strong> <span id="modalCurrentStock"></span></p>

                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <div class="btn-group w-100" role="group" aria-label="Stock action">
                            <input type="radio" class="btn-check" name="adjustAction" id="actionAdd" value="add" autocomplete="off" checked>
                            <label class="btn btn-outline-success" for="actionAdd"><i class="bi bi-plus-circle me-1"></i>Add</label>

                            <input type="radio" class="btn-check" name="adjustAction" id="actionSubtract" value="subtract" autocomplete="off">
                            <label class="btn btn-outline-danger" for="actionSubtract"><i class="bi bi-dash-circle me-1"></i>Remove</label>
                        </div>
                        <small class="text-muted" id="adjustHint">This will increase available stock.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="adjustQuantity" min="1" value="1" inputmode="numeric">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <select class="form-select" id="adjustReason">
                            <option value="Manual Stock Adjustment">Manual Adjustment</option>
                            <option value="Stock Received">Stock Received</option>
                            <option value="Stock Return">Stock Return</option>
                            <option value="Damage/Spoilage">Damage/Spoilage</option>
                            <option value="Inventory Check">Inventory Check</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="adjustStockBtn" onclick="submitStockAdjustment()">Add Stock</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Threshold Modal -->
    <div class="modal fade" id="thresholdModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Set Low Stock Threshold</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3"><strong>Item:</strong> <span id="thresholdItemName"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Alert when stock falls below:</label>
                        <input type="number" class="form-control" id="thresholdQuantity" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="submitThresholdUpdate()">Update Threshold</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentItemId = null;
        let currentStockValue = 0;

        function getSelectedAdjustAction() {
            const selected = document.querySelector('input[name="adjustAction"]:checked');
            return selected ? selected.value : 'add';
        }

        function updateAdjustmentButton() {
            const action = getSelectedAdjustAction();
            const hintEl = document.getElementById('adjustHint');
            const submitBtn = document.getElementById('adjustStockBtn');

            if (action === 'add') {
                hintEl.textContent = 'This will increase available stock.';
                submitBtn.textContent = 'Add Stock';
                submitBtn.classList.remove('btn-danger');
                submitBtn.classList.add('btn-primary');
            } else {
                hintEl.textContent = 'This will reduce available stock.';
                submitBtn.textContent = 'Remove Stock';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-danger');
            }
        }

        function openStockAdjustModal(itemId, itemName, currentStock) {
            currentItemId = itemId;
            currentStockValue = parseInt(currentStock, 10) || 0;
            document.getElementById('modalItemName').textContent = itemName;
            document.getElementById('modalCurrentStock').textContent = currentStockValue;
            document.getElementById('adjustQuantity').value = 1;
            document.getElementById('actionAdd').checked = true;
            document.getElementById('adjustReason').value = 'Manual Stock Adjustment';
            updateAdjustmentButton();
            new bootstrap.Modal(document.getElementById('stockAdjustModal')).show();
        }

        async function submitStockAdjustment() {
            const action = getSelectedAdjustAction();
            const quantity = parseInt(document.getElementById('adjustQuantity').value, 10);
            const reason = document.getElementById('adjustReason').value;

            if (!quantity || quantity <= 0) {
                alert('Please enter a valid quantity');
                return;
            }

            if (action === 'subtract' && quantity > currentStockValue) {
                alert('Cannot remove more than current stock.');
                return;
            }

            try {
                const response = await fetch('<?= base_url('admin/menu/adjust-stock') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        item_id: currentItemId,
                        action,
                        quantity,
                        reason
                    })
                });

                const responseText = await response.text();
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Non-JSON response:', responseText);
                    alert('Server returned an unexpected response. Please refresh and try again.');
                    return;
                }

                if (!data.success) {
                    alert('Error: ' + (data.message || 'Unable to adjust stock'));
                    return;
                }

                bootstrap.Modal.getInstance(document.getElementById('stockAdjustModal')).hide();
                alert('Stock updated successfully.');
                location.reload();
            } catch (error) {
                console.error(error);
                alert('Error updating stock.');
            }
        }

        function openThresholdModal(itemId, itemName, currentThreshold) {
            currentItemId = itemId;
            document.getElementById('thresholdItemName').textContent = itemName;
            document.getElementById('thresholdQuantity').value = parseInt(currentThreshold, 10) || 0;
            new bootstrap.Modal(document.getElementById('thresholdModal')).show();
        }

        async function submitThresholdUpdate() {
            const threshold = parseInt(document.getElementById('thresholdQuantity').value, 10);

            if (Number.isNaN(threshold) || threshold < 0) {
                alert('Threshold cannot be negative');
                return;
            }

            try {
                const response = await fetch('<?= base_url('admin/menu/set-threshold') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        item_id: currentItemId,
                        threshold
                    })
                });

                const responseText = await response.text();
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Non-JSON response:', responseText);
                    alert('Server returned an unexpected response. Please refresh and try again.');
                    return;
                }

                if (!data.success) {
                    alert('Error: ' + (data.message || 'Unable to update threshold'));
                    return;
                }

                bootstrap.Modal.getInstance(document.getElementById('thresholdModal')).hide();
                alert('Threshold updated successfully.');
                location.reload();
            } catch (error) {
                console.error(error);
                alert('Error updating threshold.');
            }
        }

        // Check stock levels and create alerts
        function checkStockLevels() {
            fetch('<?= base_url('admin/menu/check-stock') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`✓ Check complete!\n${data.alerts_created} new alerts created.`);
                    location.reload();
                } else {
                    alert('Error checking stock levels');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error checking stock levels');
            });
        }

        // Dismiss alert
        function dismissAlert(alertId) {
            if (!confirm('Are you sure you want to dismiss this alert?')) {
                return;
            }

            const formData = new FormData();
            formData.append('alert_id', alertId);

            fetch('<?= base_url('admin/menu/dismiss-alert') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Alert dismissed');
                    location.reload();
                } else {
                    alert('Error dismissing alert: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error dismissing alert');
            });
        }

        // Auto-refresh alerts every 5 minutes
        setInterval(() => {
            fetch('<?= base_url('admin/menu/get-alerts') ?>')
                .then(response => response.json())
                .then(data => {
                    // Update badge/notification if new alerts detected
                    if (data.stats && data.stats.total_today > 0) {
                        // Could update DOM with new alerts here
                    }
                });
        }, 5 * 60 * 1000);

        document.getElementById('actionAdd').addEventListener('change', updateAdjustmentButton);
        document.getElementById('actionSubtract').addEventListener('change', updateAdjustmentButton);
    </script>
</body>
</html>
