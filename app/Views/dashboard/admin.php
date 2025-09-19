<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Order Management</a>
            <div class="d-flex">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?= session()->get('username') ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Flashdata success message -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title"><i class="bi bi-emoji-smile"></i> Welcome, <?= session()->get('username') ?>!</h2>
                <p class="card-text">You are logged in as an administrator. From here you can manage your orders and system.</p>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam fs-1 text-primary"></i>
                        <h5 class="mt-2">Manage Orders</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-people fs-1 text-primary"></i>
                        <h5 class="mt-2">Manage Users</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-gear fs-1 text-primary"></i>
                        <h5 class="mt-2">Settings</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add this to your sidebar or menu -->
        <a href="<?= base_url('barcode-master/scan.php') ?>" class="menu-item">
            <i class="bi bi-camera"></i>
            <span>Scan Barcode</span>
        </a>


    <!-- Floating Chat Toggle Button -->
<button id="openChatBtn" class="btn btn-primary rounded-circle shadow" 
        style="position: fixed; bottom: 30px; right: 30px; z-index: 1100; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
    <i class="bi bi-chat-dots fs-3"></i>
</button>

<!-- Togglable Chat Panel -->
<div id="chatPanel" class="card shadow" 
     style="position: fixed; bottom: 100px; right: 30px; width: 350px; z-index: 1101; display: none;">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="cursor: pointer;">
        <span><i class="bi bi-chat-dots"></i> Chat</span>
        <button class="btn btn-sm btn-light" id="closeChatBtn" type="button">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="card-body p-0" style="height: 400px;">
        <iframe src="<?= base_url('Realtime-chat-application-main/users.php?user=' . session()->get('username') . '&role=' . session()->get('role')) ?>" style="width:100%; height:100%; border:none;"></iframe>
    </div>
</div>

<script>
const openChatBtn = document.getElementById('openChatBtn');
const chatPanel = document.getElementById('chatPanel');
const closeChatBtn = document.getElementById('closeChatBtn');

openChatBtn.addEventListener('click', function() {
    chatPanel.style.display = 'block';
    openChatBtn.style.display = 'none';
});
closeChatBtn.addEventListener('click', function() {
    chatPanel.style.display = 'none';
    openChatBtn.style.display = 'flex';
});
</script>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>