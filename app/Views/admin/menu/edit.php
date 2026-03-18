<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item - Admin</title>
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
        .image-preview {
            width: 220px;
            height: 220px;
            border: 2px dashed #ccc;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #f8f9fa;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 100%;
        }
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

            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2>Edit Menu Item</h2>
                        <p class="text-muted mb-0">Update details for <?= esc($menu_item['name']) ?></p>
                    </div>
                    <a href="<?= base_url('admin/menu') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Menu
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <?php if (session()->getFlashdata('errors')): ?>
                                    <div class="alert alert-danger">
                                        <strong>Validation Errors:</strong>
                                        <ul class="mb-0 mt-2">
                                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                                <li><?= esc($error) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                                <?php endif; ?>

                                <form action="<?= base_url('admin/menu/edit/' . $menu_item['id']) ?>" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Item Name *</label>
                                                <input type="text" class="form-control form-control-lg" id="name" name="name"
                                                       required value="<?= esc(old('name', $menu_item['name'])) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Price (PHP) *</label>
                                                <input type="number" class="form-control form-control-lg" id="price" name="price"
                                                       step="0.01" min="0" required value="<?= esc(old('price', $menu_item['price'])) ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Category *</label>
                                                <?php $selectedCategory = old('category', $menu_item['category']); ?>
                                                <select class="form-select form-select-lg" id="category" name="category" required>
                                                    <option value="">Select Category</option>
                                                    <option value="Coffee" <?= $selectedCategory === 'Coffee' ? 'selected' : '' ?>>Coffee</option>
                                                    <option value="Snacks" <?= $selectedCategory === 'Snacks' ? 'selected' : '' ?>>Snacks</option>
                                                    <option value="Beverages" <?= $selectedCategory === 'Beverages' ? 'selected' : '' ?>>Beverages</option>
                                                    <option value="Desserts" <?= $selectedCategory === 'Desserts' ? 'selected' : '' ?>>Desserts</option>
                                                    <option value="Other" <?= $selectedCategory === 'Other' ? 'selected' : '' ?>>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status *</label>
                                                <?php $selectedStatus = old('status', $menu_item['status']); ?>
                                                <select class="form-select form-select-lg" id="status" name="status" required>
                                                    <option value="available" <?= $selectedStatus === 'available' ? 'selected' : '' ?>>Available</option>
                                                    <option value="unavailable" <?= $selectedStatus === 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"
                                                  placeholder="Describe the item...\"><?= esc(old('description', $menu_item['description'])) ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="image" class="form-label">Replace Image (Optional)</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                                        <small class="text-muted">Leave empty to keep the current image.</small>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Image Preview</label>
                                        <div class="image-preview" id="imagePreview">
                                            <?php if (!empty($menu_item['image'])): ?>
                                                <img src="<?= base_url('uploads/menu/' . $menu_item['image']) ?>" alt="Current image">
                                            <?php else: ?>
                                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-save me-2"></i>Update Menu Item
                                        </button>
                                        <a href="<?= base_url('admin/menu') ?>" class="btn btn-outline-secondary btn-lg">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Current Info</h6>
                                <p class="mb-1"><strong>ID:</strong> #<?= esc($menu_item['id']) ?></p>
                                <p class="mb-1"><strong>Stock:</strong> <?= esc($menu_item['stock_quantity']) ?></p>
                                <p class="mb-1"><strong>Low Stock Threshold:</strong> <?= esc($menu_item['low_stock_threshold']) ?></p>
                                <p class="mb-0"><strong>Status:</strong> <?= esc(ucfirst($menu_item['status'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
