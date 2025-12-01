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
        .stock-low { color: #ffc107; }
        .stock-critical { color: #dc3545; }
        .stock-good { color: #28a745; }
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
                    <a href="<?= base_url('admin/inventory') ?>" class="nav-link active"><i class="bi bi-box-seam me-2"></i>Inventory</a>
                    <a href="<?= base_url('admin/menu') ?>" class="nav-link"><i class="bi bi-cup-hot me-2"></i>Menu Items</a>
                    <a href="<?= base_url('admin/users') ?>" class="nav-link"><i class="bi bi-people me-2"></i>Users</a>
                    <a href="<?= base_url('admin/activity-logs') ?>" class="nav-link"><i class="bi bi-activity me-2"></i>Activity Logs</a>
                    <a href="<?= base_url('admin/sms-logs') ?>" class="nav-link"><i class="bi bi-chat-text me-2"></i>SMS Logs</a>
                    <a href="<?= base_url('pos') ?>" class="nav-link"><i class="bi bi-shop me-2"></i>POS System</a>
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
                        <h2><i class="bi bi-box-seam me-2"></i>Inventory Management</h2>
                        <p class="text-muted">Monitor and manage stock levels</p>
                    </div>
                    <a href="<?= base_url('admin/inventory/report') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-text me-2"></i>View Reports
                    </a>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Alert Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-danger">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-danger mb-1">Out of Stock</h6>
                                        <h3 class="mb-0"><?= count($out_of_stock_items) ?></h3>
                                    </div>
                                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-warning mb-1">Low Stock</h6>
                                        <h3 class="mb-0"><?= count($low_stock_items) ?></h3>
                                    </div>
                                    <i class="bi bi-exclamation-circle text-warning" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-success mb-1">Total Items</h6>
                                        <h3 class="mb-0"><?= count($menu_items) ?></h3>
                                    </div>
                                    <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Table -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>All Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Item Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Current Stock</th>
                                        <th>Threshold</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($menu_items as $item): 
                                        $stockClass = 'stock-good';
                                        $stockIcon = 'check-circle';
                                        if ($item['stock_quantity'] == 0) {
                                            $stockClass = 'stock-critical';
                                            $stockIcon = 'x-circle';
                                        } elseif ($item['stock_quantity'] <= $item['low_stock_threshold']) {
                                            $stockClass = 'stock-low';
                                            $stockIcon = 'exclamation-circle';
                                        }
                                    ?>
                                    <tr>
                                        <td><?= $item['id'] ?></td>
                                        <td>
                                            <strong><?= esc($item['name']) ?></strong>
                                        </td>
                                        <td><span class="badge bg-secondary"><?= esc($item['category']) ?></span></td>
                                        <td>₱<?= number_format($item['price'], 2) ?></td>
                                        <td class="<?= $stockClass ?>">
                                            <i class="bi bi-<?= $stockIcon ?> me-1"></i>
                                            <strong><?= $item['stock_quantity'] ?></strong>
                                        </td>
                                        <td><?= $item['low_stock_threshold'] ?></td>
                                        <td>
                                            <?php if ($item['status'] === 'available'): ?>
                                                <span class="badge bg-success">Available</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Unavailable</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="showUpdateStockModal(<?= $item['id'] ?>, '<?= esc($item['name']) ?>', <?= $item['stock_quantity'] ?>)">
                                                <i class="bi bi-plus-circle me-1"></i>Update Stock
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Stock Changes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
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
                                    <?php foreach ($recent_logs as $log): ?>
                                    <tr>
                                        <td><?= date('M d, Y H:i', strtotime($log['created_at'])) ?></td>
                                        <td><?= esc($log['item_name']) ?></td>
                                        <td>
                                            <?php
                                            $badgeClass = $log['action'] === 'add' ? 'bg-success' : 
                                                         ($log['action'] === 'deduct' ? 'bg-danger' : 'bg-warning');
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($log['action']) ?></span>
                                        </td>
                                        <td><?= $log['quantity_change'] > 0 ? '+' : '' ?><?= $log['quantity_change'] ?></td>
                                        <td><?= $log['previous_stock'] ?></td>
                                        <td><?= $log['new_stock'] ?></td>
                                        <td><?= esc($log['username']) ?></td>
                                        <td><?= esc($log['notes']) ?></td>
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

    <!-- Update Stock Modal -->
    <div class="modal fade" id="updateStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="stock_item_id">
                    <div class="mb-3">
                        <label class="form-label"><strong>Item:</strong></label>
                        <p id="stock_item_name" class="text-muted"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Current Stock:</strong></label>
                        <p id="stock_current" class="h4 text-primary"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select id="stock_action" class="form-select">
                            <option value="add">Add to existing stock</option>
                            <option value="set">Set absolute value</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" id="stock_quantity" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea id="stock_notes" class="form-control" rows="2" placeholder="Reason for stock adjustment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateStock()">
                        <i class="bi bi-save me-2"></i>Update Stock
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let updateStockModal;

        document.addEventListener('DOMContentLoaded', function() {
            updateStockModal = new bootstrap.Modal(document.getElementById('updateStockModal'));
        });

        function showUpdateStockModal(itemId, itemName, currentStock) {
            document.getElementById('stock_item_id').value = itemId;
            document.getElementById('stock_item_name').textContent = itemName;
            document.getElementById('stock_current').textContent = currentStock;
            document.getElementById('stock_quantity').value = '';
            document.getElementById('stock_notes').value = '';
            updateStockModal.show();
        }

        async function updateStock() {
            const itemId = document.getElementById('stock_item_id').value;
            const action = document.getElementById('stock_action').value;
            const quantity = document.getElementById('stock_quantity').value;
            const notes = document.getElementById('stock_notes').value;

            if (!quantity || quantity < 0) {
                alert('Please enter a valid quantity');
                return;
            }

            try {
                const response = await fetch('<?= base_url('admin/inventory/update-stock') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `item_id=${itemId}&action=${action}&quantity=${quantity}&notes=${encodeURIComponent(notes)}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ ' + data.message);
                    updateStockModal.hide();
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating stock');
            }
        }
    </script>
</body>
</html>
