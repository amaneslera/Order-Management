<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu Item - Admin</title>
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
            width: 200px;
            height: 200px;
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
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4">
                    <h4><i class="bi bi-shield-check me-2"></i>Admin Panel</h4>
                    <hr class="border-light">
                </div>
                <nav class="nav flex-column">
                    <a href="/admin/dashboard" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a href="/admin/reports" class="nav-link"><i class="bi bi-graph-up me-2"></i>Reports</a>
                    <a href="/admin/menu" class="nav-link active"><i class="bi bi-cup-hot me-2"></i>Menu Items</a>
                    <a href="/admin/users" class="nav-link"><i class="bi bi-people me-2"></i>Users</a>
                    <a href="/admin/activity-logs" class="nav-link"><i class="bi bi-activity me-2"></i>Activity Logs</a>
                    <hr class="border-light">
                    <a href="/logout" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2>Add New Menu Item</h2>
                        <p class="text-muted">Create a new coffee or snack item</p>
                    </div>
                    <a href="/admin/menu" class="btn btn-outline-secondary">
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

                                <form action="/admin/menu/add" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Item Name *</label>
                                                <input type="text" class="form-control form-control-lg" id="name" name="name" 
                                                       placeholder="e.g., Caramel Macchiato" required value="<?= old('name') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Price (₱) *</label>
                                                <input type="number" class="form-control form-control-lg" id="price" name="price" 
                                                       step="0.01" min="0" placeholder="0.00" required value="<?= old('price') ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Category *</label>
                                                <select class="form-select form-select-lg" id="category" name="category" required>
                                                    <option value="">Select Category</option>
                                                    <option value="Coffee" <?= old('category') === 'Coffee' ? 'selected' : '' ?>>Coffee</option>
                                                    <option value="Snacks" <?= old('category') === 'Snacks' ? 'selected' : '' ?>>Snacks</option>
                                                    <option value="Beverages" <?= old('category') === 'Beverages' ? 'selected' : '' ?>>Beverages</option>
                                                    <option value="Desserts" <?= old('category') === 'Desserts' ? 'selected' : '' ?>>Desserts</option>
                                                    <option value="Other" <?= old('category') === 'Other' ? 'selected' : '' ?>>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status *</label>
                                                <select class="form-select form-select-lg" id="status" name="status" required>
                                                    <option value="available" <?= old('status') === 'available' ? 'selected' : '' ?>>Available</option>
                                                    <option value="unavailable" <?= old('status') === 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" 
                                                  placeholder="Describe the item, ingredients, or special features..."><?= old('description') ?></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label for="image" class="form-label">Item Image *</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required onchange="previewImage(event)">
                                        <small class="text-muted">Maximum file size: 2MB. Supported formats: JPG, PNG, GIF</small>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Image Preview</label>
                                        <div class="image-preview" id="imagePreview">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-save me-2"></i>Save Menu Item
                                        </button>
                                        <a href="/admin/menu" class="btn btn-outline-secondary btn-lg">
                                            Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Tips</h6>
                                <ul class="small mb-0">
                                    <li>Use clear, descriptive names</li>
                                    <li>Upload high-quality images (recommended: 800x800px)</li>
                                    <li>Set accurate prices</li>
                                    <li>Choose the correct category</li>
                                    <li>Write appealing descriptions</li>
                                    <li>Mark as "Available" when ready to sell</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-star me-2"></i>Popular Categories</h6>
                                <div class="d-flex flex-column gap-2">
                                    <span class="badge bg-primary">Coffee</span>
                                    <span class="badge bg-success">Snacks</span>
                                    <span class="badge bg-info">Beverages</span>
                                    <span class="badge bg-warning text-dark">Desserts</span>
                                </div>
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
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
