<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Admin</title>
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
        .stock-badge-ok { background-color: #28a745; color: white; }
        .stock-badge-low { background-color: #ffc107; color: black; }
        .stock-badge-critical { background-color: #dc3545; color: white; }
        .stock-bar {
            height: 30px;
            background: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }
        .stock-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        .stock-fill.low {
            background: linear-gradient(90deg, #ffc107, #fd7e14);
        }
        .stock-fill.critical {
            background: linear-gradient(90deg, #dc3545, #c82333);
        }
        .menu-item-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .projected-stock-box {
            border-radius: 8px;
            padding: 12px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .projected-stock-value {
            font-size: 1.25rem;
            font-weight: 700;
        }
        .projected-safe { color: #198754; }
        .projected-warning { color: #fd7e14; }
        .projected-danger { color: #dc3545; }
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
                    <a href="<?= base_url('admin/menu/inventory') ?>" class="nav-link active"><i class="bi bi-box-seam me-2"></i>Inventory</a>
                    <a href="<?= base_url('admin/menu/alerts') ?>" class="nav-link"><i class="bi bi-exclamation-triangle me-2"></i>Stock Alerts</a>
                    <a href="<?= base_url('admin/users') ?>" class="nav-link"><i class="bi bi-people me-2"></i>Users</a>
                    <a href="<?= base_url('admin/sms-logs') ?>" class="nav-link"><i class="bi bi-chat-text me-2"></i>SMS Logs</a>
                    <a href="<?= base_url('admin/activity-logs') ?>" class="nav-link"><i class="bi bi-activity me-2"></i>Activity Logs</a>
                    <a href="<?= base_url('pos') ?>" class="nav-link" target="_blank"><i class="bi bi-shop me-2"></i>Open Cashier POS</a>
                    <hr class="border-light">
                    <a href="<?= base_url('kiosk') ?>" class="nav-link" target="_blank"><i class="bi bi-phone me-2"></i>View Kiosk</a>
                    <a href="<?= base_url('barcode-master/scan.php') ?>" class="nav-link" target="_blank"><i class="bi bi-upc-scan me-2"></i>Barcode Scanner</a>
                    <hr class="border-light">
                    <a href="<?= base_url('logout') ?>" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2 class="mb-4"><i class="bi bi-box-seam me-2"></i>Inventory Management</h2>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <h3><?= $total_items ?></h3>
                            <p class="text-muted mb-0">Total Items</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <h3 class="text-success"><?= $total_items - $low_stock_items - $out_of_stock_items ?></h3>
                            <p class="text-muted mb-0">In Stock</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <h3 class="text-warning"><?= $low_stock_items ?></h3>
                            <p class="text-muted mb-0">Low Stock</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <h3 class="text-danger"><?= $out_of_stock_items ?></h3>
                            <p class="text-muted mb-0">Out of Stock</p>
                        </div>
                    </div>
                </div>

                <!-- Inventory Table -->
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-0">Stock Levels</h5>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search items...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Current Stock</th>
                                        <th>Stock Level</th>
                                        <th>Low Stock Alert</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($menu_items as $item): ?>
                                    <tr class="item-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($item['image'] && file_exists(FCPATH . 'uploads/menu/' . $item['image'])): ?>
                                                    <img src="<?= base_url('uploads/menu/' . $item['image']) ?>" alt="<?= $item['name'] ?>" class="menu-item-img me-2">
                                                <?php else: ?>
                                                    <div class="menu-item-img me-2 bg-secondary d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-cup-hot text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <strong><?= esc($item['name']) ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= esc($item['category']) ?></span>
                                        </td>
                                        <td>
                                            <strong>₱<?= number_format($item['price'], 2) ?></strong>
                                        </td>
                                        <td>
                                            <span id="qty-<?= $item['id'] ?>"><?= $item['stock_quantity'] ?></span>
                                        </td>
                                        <td>
                                            <div class="stock-bar">
                                                <?php
                                                $maxStock = 100;
                                                $percentage = min(($item['stock_quantity'] / $maxStock) * 100, 100);
                                                $stockClass = '';
                                                if ($item['stock_quantity'] === 0) {
                                                    $stockClass = 'critical';
                                                } elseif ($item['stock_quantity'] <= $item['low_stock_threshold']) {
                                                    $stockClass = 'low';
                                                }
                                                ?>
                                                <div class="stock-fill <?= $stockClass ?>" style="width: <?= $percentage ?>%">
                                                    <?php if ($percentage >= 20): ?><?= $item['stock_quantity'] ?><?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span id="threshold-<?= $item['id'] ?>"><?= $item['low_stock_threshold'] ?></span>
                                                <button class="btn btn-sm btn-outline-warning" onclick="openThresholdModal(<?= $item['id'] ?>, '<?= esc($item['name']) ?>', <?= $item['low_stock_threshold'] ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            if ($item['stock_quantity'] === 0) {
                                                echo '<span class="badge stock-badge-critical">Out of Stock</span>';
                                            } elseif ($item['stock_quantity'] <= $item['low_stock_threshold']) {
                                                echo '<span class="badge stock-badge-low">Low Stock</span>';
                                            } else {
                                                echo '<span class="badge stock-badge-ok">OK</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="openStockAdjustModal(<?= $item['id'] ?>, '<?= esc($item['name']) ?>', <?= $item['stock_quantity'] ?>)">
                                                <i class="bi bi-pencil"></i> Edit Inventory
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Stock Changes -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Stock Changes</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Item</th>
                                        <th>Action</th>
                                        <th>Change</th>
                                        <th>Previous</th>
                                        <th>New</th>
                                        <th>User</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_logs)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No stock changes yet.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_logs as $log): ?>
                                            <tr>
                                                <td><?= date('M d, Y H:i', strtotime($log['created_at'])) ?></td>
                                                <td><?= esc($log['item_name']) ?></td>
                                                <td>
                                                    <?php if ($log['action'] === 'add'): ?>
                                                        <span class="badge bg-success">Add</span>
                                                    <?php elseif ($log['action'] === 'deduct'): ?>
                                                        <span class="badge bg-danger">Deduct</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><?= esc(ucfirst($log['action'])) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $log['quantity_change'] > 0 ? '+' : '' ?><?= esc($log['quantity_change']) ?></td>
                                                <td><?= esc($log['previous_stock']) ?></td>
                                                <td><?= esc($log['new_stock']) ?></td>
                                                <td><?= esc($log['username'] ?? '-') ?></td>
                                                <td><?= esc($log['notes'] ?? '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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
                    <p class="mb-3"><strong>Item:</strong> <span id="modalItemName"></span></p>
                    <p class="mb-3"><strong>Current Stock:</strong> <span id="modalCurrentStock"></span></p>
                    
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <div class="btn-group w-100" role="group" aria-label="Stock action">
                            <input type="radio" class="btn-check" name="adjustAction" id="actionAdd" value="add" autocomplete="off" checked>
                            <label class="btn btn-outline-success" for="actionAdd"><i class="bi bi-plus-circle me-1"></i>Add Stock</label>

                            <input type="radio" class="btn-check" name="adjustAction" id="actionSubtract" value="subtract" autocomplete="off">
                            <label class="btn btn-outline-danger" for="actionSubtract"><i class="bi bi-dash-circle me-1"></i>Remove Stock</label>
                        </div>
                        <small class="text-muted" id="adjustHint">This will increase available stock.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="adjustQuantity" min="1" value="1" inputmode="numeric">
                    </div>

                    <div class="mb-3 projected-stock-box">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Projected Stock</span>
                            <span id="projectedStock" class="projected-stock-value projected-safe">0</span>
                        </div>
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
                        <input type="number" class="form-control form-control-lg" id="thresholdQuantity" min="0">
                        <small class="text-muted">Staff will be notified when stock reaches this level</small>
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

        function updateAdjustmentPreview() {
            const action = getSelectedAdjustAction();
            const quantity = parseInt(document.getElementById('adjustQuantity').value) || 0;
            const projectedEl = document.getElementById('projectedStock');
            const hintEl = document.getElementById('adjustHint');
            const submitBtn = document.getElementById('adjustStockBtn');

            let projected = currentStockValue;
            if (action === 'add') {
                projected = currentStockValue + quantity;
                hintEl.textContent = 'This will increase available stock.';
                submitBtn.textContent = 'Add Stock';
                submitBtn.classList.remove('btn-danger');
                submitBtn.classList.add('btn-primary');
            } else {
                projected = currentStockValue - quantity;
                hintEl.textContent = 'This will reduce available stock.';
                submitBtn.textContent = 'Remove Stock';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-danger');
            }

            projectedEl.textContent = projected;
            projectedEl.classList.remove('projected-safe', 'projected-warning', 'projected-danger');

            if (projected < 0) {
                projectedEl.classList.add('projected-danger');
                hintEl.textContent = 'Quantity is too high. Stock cannot go below 0.';
                submitBtn.disabled = true;
                return;
            }

            submitBtn.disabled = quantity <= 0;

            if (projected === 0) {
                projectedEl.classList.add('projected-warning');
            } else {
                projectedEl.classList.add('projected-safe');
            }
        }

        // Open stock adjust modal
        function openStockAdjustModal(itemId, itemName, currentStock) {
            currentItemId = itemId;
            currentStockValue = parseInt(currentStock) || 0;
            document.getElementById('modalItemName').textContent = itemName;
            document.getElementById('modalCurrentStock').textContent = currentStock;
            document.getElementById('adjustQuantity').value = 1;
            document.getElementById('actionAdd').checked = true;
            document.getElementById('adjustReason').value = 'Manual Stock Adjustment';
            updateAdjustmentPreview();
            new bootstrap.Modal(document.getElementById('stockAdjustModal')).show();
        }

        // Submit stock adjustment
        async function submitStockAdjustment() {
            const action = getSelectedAdjustAction();
            const quantity = parseInt(document.getElementById('adjustQuantity').value);
            const reason = document.getElementById('adjustReason').value;

            if (quantity <= 0) {
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
                        action: action,
                        quantity: quantity,
                        reason: reason
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

                if (data.success) {
                    document.getElementById('qty-' + currentItemId).textContent = data.new_stock;
                    bootstrap.Modal.getInstance(document.getElementById('stockAdjustModal')).hide();
                    alert('Stock adjusted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Error: ' + error);
            }
        }

        document.getElementById('adjustQuantity').addEventListener('input', updateAdjustmentPreview);
        document.getElementById('actionAdd').addEventListener('change', updateAdjustmentPreview);
        document.getElementById('actionSubtract').addEventListener('change', updateAdjustmentPreview);

        // Open threshold modal
        function openThresholdModal(itemId, itemName, currentThreshold) {
            currentItemId = itemId;
            document.getElementById('thresholdItemName').textContent = itemName;
            document.getElementById('thresholdQuantity').value = currentThreshold;
            new bootstrap.Modal(document.getElementById('thresholdModal')).show();
        }

        // Submit threshold update
        async function submitThresholdUpdate() {
            const threshold = parseInt(document.getElementById('thresholdQuantity').value);

            if (threshold < 0) {
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
                        threshold: threshold
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

                if (data.success) {
                    document.getElementById('threshold-' + currentItemId).textContent = threshold;
                    bootstrap.Modal.getInstance(document.getElementById('thresholdModal')).hide();
                    alert('Low stock threshold updated!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Error: ' + error);
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.item-row').forEach(row => {
                const itemName = row.querySelector('strong').textContent.toLowerCase();
                if (itemName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
