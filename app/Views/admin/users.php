<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin</title>
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
                <a href="<?= base_url('admin/menu/alerts') ?>" class="nav-link"><i class="bi bi-exclamation-triangle me-2"></i>Stock Alerts</a>
                <a href="<?= base_url('admin/users') ?>" class="nav-link active"><i class="bi bi-people me-2"></i>Users</a>
                <a href="<?= base_url('admin/sms-logs') ?>" class="nav-link"><i class="bi bi-chat-text me-2"></i>SMS Logs</a>
                <a href="<?= base_url('admin/activity-logs') ?>" class="nav-link"><i class="bi bi-activity me-2"></i>Activity Logs</a>
                <a href="<?= base_url('pos') ?>" class="nav-link"><i class="bi bi-shop me-2"></i>Open Cashier POS</a>
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
                    <h2><i class="bi bi-people me-2"></i>User Management</h2>
                    <p class="text-muted mb-0">Manage admin and cashier accounts</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus me-2"></i>Add User
                </button>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Users</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No users found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= (int) $user['id'] ?></td>
                                            <td><strong><?= esc($user['username']) ?></strong></td>
                                            <td>
                                                <span class="badge <?= ($user['role'] === 'Admin') ? 'bg-danger' : 'bg-secondary' ?>">
                                                    <?= esc($user['role']) ?>
                                                </span>
                                            </td>
                                            <td><?= !empty($user['created_at']) ? date('M d, Y h:i A', strtotime($user['created_at'])) : '-' ?></td>
                                            <td class="text-end">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    onclick='openEditUserModal(<?= (int) $user['id'] ?>, <?= json_encode($user['username']) ?>, <?= json_encode($user['role']) ?>)'>
                                                    <i class="bi bi-pencil-square me-1"></i>Edit
                                                </button>
                                                <?php if ((int) $user['id'] !== (int) session()->get('user_id')): ?>
                                                    <a href="<?= base_url('admin/users/delete/' . $user['id']) ?>"
                                                       class="btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('Delete this user?');">
                                                        <i class="bi bi-trash me-1"></i>Delete
                                                    </a>
                                                <?php endif; ?>
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

<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= base_url('admin/users/add') ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="4">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="cashier">Cashier</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editUserForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="editUsername" class="form-control" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (optional)</label>
                        <input type="password" name="password" class="form-control" minlength="4">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="editRole" class="form-select" required>
                            <option value="cashier">Cashier</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openEditUserModal(userId, username, role) {
        const normalizedRole = String(role).toLowerCase() === 'admin' ? 'Admin' : 'cashier';
        document.getElementById('editUserForm').action = '<?= base_url('admin/users/edit') ?>/' + userId;
        document.getElementById('editUsername').value = username;
        document.getElementById('editRole').value = normalizedRole;
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }
</script>
</body>
</html>
