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
                    <a href="<?= base_url('admin/inventory') ?>" class="nav-link"><i class="bi bi-box-seam me-2"></i>Inventory</a>
                    <a href="<?= base_url('admin/users') ?>" class="nav-link"><i class="bi bi-people me-2"></i>Users</a>
                    <a href="<?= base_url('admin/activity-logs') ?>" class="nav-link"><i class="bi bi-activity me-2"></i>Activity Logs</a>
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
                        <h2>Menu Management</h2>
                        <p class="text-muted">Manage coffee and snack items</p>
                    </div>
                    <a href="<?= base_url('admin/menu/add') ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>Add New Item
                    </a>
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
                                        <tr data-category="<?= esc($item['category']) ?>">
                                            <td>
                                                <?php if ($item['image']): ?>
                                                    <img src="/uploads/menu/<?= esc($item['image']) ?>" alt="<?= esc($item['name']) ?>" class="menu-item-img">
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
                                                           onchange="toggleStatus(<?= $item['id'] ?>)">
                                                    <label class="form-check-label small">
                                                        <?= ucfirst($item['status']) ?>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="/admin/menu/edit/<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <a href="/admin/menu/delete/<?= $item['id'] ?>" 
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

        function toggleStatus(itemId) {
            fetch(`/admin/menu/toggle-status/${itemId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const label = event.target.nextElementSibling;
                    label.textContent = data.status === 'available' ? 'Available' : 'Unavailable';
                } else {
                    alert('Failed to update status');
                    event.target.checked = !event.target.checked;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                event.target.checked = !event.target.checked;
            });
        }
    </script>
</body>
</html>
