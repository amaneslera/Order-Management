<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Dashboard - Coffee Kiosk</title>
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
        .order-card {
            border-left: 4px solid #ffc107;
            transition: all 0.3s;
        }
        .order-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .status-pending { border-left-color: #ffc107; }
        .status-paid { border-left-color: #28a745; }
        .status-completed { border-left-color: #007bff; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4">
                    <h4><i class="bi bi-shop me-2"></i>POS System</h4>
                    <hr class="border-light">
                </div>
                <nav class="nav flex-column">
                    <a href="<?= base_url('pos') ?>" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a href="<?= base_url('staff/send-sms') ?>" class="nav-link"><i class="bi bi-chat-text me-2"></i>Message Admin</a>
                    <a href="<?= base_url('pos/search') ?>" class="nav-link"><i class="bi bi-search me-2"></i>Search Order</a>
                    <a href="<?= base_url('pos/orders') ?>" class="nav-link"><i class="bi bi-list-ul me-2"></i>All Orders</a>
                    <?php if (session()->get('role') === 'Admin'): ?>
                        <a href="<?= base_url('admin') ?>" class="nav-link"><i class="bi bi-gear me-2"></i>Admin Panel</a>
                    <?php endif; ?>
                    <hr class="border-light">
                    <a href="<?= base_url('barcode-master/scan.php') ?>" class="nav-link" target="_blank"><i class="bi bi-upc-scan me-2"></i>Scan Barcode</a>
                    <a href="#" id="openChatLink" class="nav-link"><i class="bi bi-chat-dots me-2"></i>Messages</a>
                    <hr class="border-light">
                    <a href="<?= base_url('logout') ?>" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </nav>
                <div class="p-4 mt-auto">
                    <small class="text-light">
                        <i class="bi bi-person-circle me-2"></i>
                        <?= esc(session()->get('name')) ?><br>
                        <span class="badge bg-light text-dark mt-1"><?= ucfirst(session()->get('role')) ?></span>
                    </small>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2>POS Dashboard</h2>
                        <p class="text-muted">Welcome back, <?= esc(session()->get('name')) ?>!</p>
                    </div>
                    <div>
                        <span class="text-muted"><?= date('l, F d, Y') ?></span>
                    </div>
                </div>

                <!-- Quick Search -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5><i class="bi bi-search me-2"></i>Quick Order Search</h5>
                        <form action="<?= base_url('pos/search') ?>" method="GET" class="row g-3">
                            <div class="col-md-8">
                                <input type="text" name="order_number" class="form-control form-control-lg" 
                                       placeholder="Enter or scan order number (e.g., A001, B234)" 
                                       autofocus 
                                       required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-search me-2"></i>Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Pending Orders</h6>
                                        <h2><?= count($pending_orders) ?></h2>
                                    </div>
                                    <div class="text-warning">
                                        <i class="bi bi-clock-history" style="font-size: 3rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Today's Orders</h6>
                                        <h2><?= count($today_orders) ?></h2>
                                    </div>
                                    <div class="text-primary">
                                        <i class="bi bi-receipt" style="font-size: 3rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Total Revenue</h6>
                                        <h2>₱<?= number_format(array_sum(array_column($today_orders, 'total_amount')), 2) ?></h2>
                                    </div>
                                    <div class="text-success">
                                        <i class="bi bi-cash-stack" style="font-size: 3rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Orders -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Pending Orders</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pending_orders)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-2">No pending orders</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($pending_orders as $order): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card order-card status-<?= $order['status'] ?>">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-2">
                                                <h6 class="mb-0">
                                                    <i class="bi bi-hash"></i><?= esc($order['order_number']) ?>
                                                </h6>
                                                <span class="badge bg-warning"><?= ucfirst($order['status']) ?></span>
                                            </div>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-clock me-1"></i>
                                                <?= date('h:i A', strtotime($order['created_at'])) ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong class="text-primary">₱<?= number_format($order['total_amount'], 2) ?></strong>
                                                <a href="<?= base_url('pos/order/' . $order['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                    View Details <i class="bi bi-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Chat Toggle Button -->
    <button id="openChatBtn" class="btn btn-primary rounded-circle shadow" 
            style="position: fixed; bottom: 30px; right: 30px; z-index: 1100; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-chat-dots fs-3"></i>
    </button>

    <!-- Togglable Chat Panel -->
    <div id="chatPanel" class="card shadow" 
         style="position: fixed; bottom: 100px; right: 30px; width: 400px; z-index: 1101; display: none;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-chat-dots me-2"></i>Chat</span>
            <button class="btn btn-sm btn-light" id="closeChatBtn" type="button">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="card-body p-0" style="height: 500px;">
            <iframe src="<?= base_url('Realtime-chat-application-main/users.php?user=' . session()->get('username') . '&role=' . session()->get('role')) ?>" 
                    style="width:100%; height:100%; border:none;"></iframe>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const openChatBtn = document.getElementById('openChatBtn');
        const openChatLink = document.getElementById('openChatLink');
        const chatPanel = document.getElementById('chatPanel');
        const closeChatBtn = document.getElementById('closeChatBtn');

        function openChat() {
            chatPanel.style.display = 'block';
            openChatBtn.style.display = 'none';
        }

        function closeChat() {
            chatPanel.style.display = 'none';
            openChatBtn.style.display = 'flex';
        }

        openChatBtn.addEventListener('click', openChat);
        openChatLink.addEventListener('click', function(e) {
            e.preventDefault();
            openChat();
        });
        closeChatBtn.addEventListener('click', closeChat);
    </script>
</body>
</html>
