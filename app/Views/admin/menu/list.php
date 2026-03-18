<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Admin</title>
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
        .menu-item-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
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
                    <a href="<?= base_url('admin/menu') ?>" class="nav-link active"><i class="bi bi-cup-hot me-2"></i>Menu Items</a>
                    <a href="<?= base_url('admin/menu/inventory') ?>" class="nav-link"><i class="bi bi-box-seam me-2"></i>Inventory</a>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2>Menu Management</h2>
                        <p class="text-muted">Manage coffee and snack items</p>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="bi bi-plus-circle me-2"></i>Add New Item
                    </button>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter by Category -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="mb-0">Filter by Category:</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-outline-primary active" onclick="filterCategory('all')">All</button>
                                    <?php 
                                    $uniqueCategories = array_unique(array_column($menu_items, 'category'));
                                    foreach ($uniqueCategories as $cat): 
                                    ?>
                                        <button class="btn btn-outline-primary" onclick="filterCategory('<?= esc($cat) ?>')">
                                            <?= esc($cat) ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Items Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="menuTableBody">
                                    <?php if (empty($menu_items)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                                <p class="mt-2">No menu items found</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($menu_items as $item): ?>
                                        <?php
                                            $imagePathPrimary = FCPATH . 'uploads/menu/' . ($item['image'] ?? '');
                                            $imagePathPublic = ROOTPATH . 'public/uploads/menu/' . ($item['image'] ?? '');
                                            $imageExists = ! empty($item['image']) && (is_file($imagePathPrimary) || is_file($imagePathPublic));
                                            $imageUrl = base_url('uploads/menu/' . ($item['image'] ?? ''));
                                        ?>
                                        <tr data-category="<?= esc($item['category']) ?>">
                                            <td>
                                                <?php if ($imageExists): ?>
                                                    <img src="<?= $imageUrl ?>" alt="<?= esc($item['name']) ?>" class="menu-item-img">
                                                <?php else: ?>
                                                    <div class="menu-item-img bg-light d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-cup-hot text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= esc($item['name']) ?></strong>
                                                <?php if ($item['description']): ?>
                                                    <br><small class="text-muted"><?= esc(substr($item['description'], 0, 50)) ?>...</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?= esc($item['category']) ?></span>
                                            </td>
                                            <td><strong class="text-primary">₱<?= number_format($item['price'], 2) ?></strong></td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" 
                                                           <?= $item['status'] === 'available' ? 'checked' : '' ?>
                                                           onchange="toggleStatus(<?= $item['id'] ?>, this)">
                                                    <label class="form-check-label small">
                                                        <?= ucfirst($item['status']) ?>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-primary"
                                                        onclick="openEditModal(this)"
                                                        data-id="<?= esc($item['id'], 'attr') ?>"
                                                        data-name="<?= esc($item['name'], 'attr') ?>"
                                                        data-category="<?= esc($item['category'], 'attr') ?>"
                                                        data-price="<?= esc($item['price'], 'attr') ?>"
                                                        data-description="<?= esc($item['description'] ?? '', 'attr') ?>"
                                                        data-status="<?= esc($item['status'], 'attr') ?>">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </button>
                                                    <a href="<?= base_url('admin/menu/delete/' . $item['id']) ?>" 
                                                       class="btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('Are you sure you want to delete this item?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </td>
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

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('admin/menu/add') ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="add_name" class="form-label">Item Name *</label>
                                <input type="text" class="form-control" id="add_name" name="name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="add_price" class="form-label">Price *</label>
                                <input type="number" class="form-control" id="add_price" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_category" class="form-label">Category *</label>
                                <select class="form-select" id="add_category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Coffee">Coffee</option>
                                    <option value="Pastries">Pastries</option>
                                    <option value="Beverages">Beverages</option>
                                    <option value="Desserts">Desserts</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="add_status" class="form-label">Status *</label>
                                <select class="form-select" id="add_status" name="status" required>
                                    <option value="available" selected>Available</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="add_description" class="form-label">Description</label>
                            <textarea class="form-control" id="add_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="add_image" class="form-label">Item Image *</label>
                            <input type="file" class="form-control" id="add_image" name="image" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editItemForm" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="edit_name" class="form-label">Item Name *</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_price" class="form-label">Price *</label>
                                <input type="number" class="form-control" id="edit_price" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_category" class="form-label">Category *</label>
                                <select class="form-select" id="edit_category" name="category" required>
                                    <option value="Coffee">Coffee</option>
                                    <option value="Pastries">Pastries</option>
                                    <option value="Beverages">Beverages</option>
                                    <option value="Desserts">Desserts</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_status" class="form-label">Status *</label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="available">Available</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_image" class="form-label">Replace Image (Optional)</label>
                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                            <small class="text-muted">Leave empty to keep the current image.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterCategory(category) {
            // Update button states
            document.querySelectorAll('.btn-group .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // Filter table rows
            const rows = document.querySelectorAll('#menuTableBody tr');
            rows.forEach(row => {
                if (category === 'all' || row.dataset.category === category) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function openEditModal(button) {
            const data = button.dataset;

            document.getElementById('editItemForm').action = `<?= base_url('admin/menu/edit') ?>/${data.id}`;
            document.getElementById('edit_name').value = data.name || '';
            document.getElementById('edit_category').value = data.category || 'Other';
            document.getElementById('edit_price').value = data.price || '';
            document.getElementById('edit_description').value = data.description || '';
            document.getElementById('edit_status').value = data.status || 'available';
            document.getElementById('edit_image').value = '';

            const modal = new bootstrap.Modal(document.getElementById('editItemModal'));
            modal.show();
        }

        function toggleStatus(itemId, el) {
            fetch(`<?= base_url('admin/menu/toggle-status') ?>/${itemId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const label = el.nextElementSibling;
                    label.textContent = data.status === 'available' ? 'Available' : 'Unavailable';
                } else {
                    alert('Failed to update status');
                    el.checked = !el.checked;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                el.checked = !el.checked;
            });
        }
    </script>
</body>
</html>
