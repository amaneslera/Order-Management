<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Order - POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary: #6B4423; }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .search-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        .search-icon {
            font-size: 4rem;
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-4">
                    <a href="<?= base_url('pos') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>

                <div class="search-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-search search-icon"></i>
                        <h2 class="mt-3">Search Order</h2>
                        <p class="text-muted">Enter or scan the order number</p>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('pos/search') ?>" method="GET">
                        <div class="mb-4">
                            <label for="order_number" class="form-label h5">Order Number</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="bi bi-upc-scan"></i>
                                </span>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="order_number" 
                                       name="order_number" 
                                       placeholder="A001" 
                                       autofocus 
                                       required>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                You can manually type or scan the barcode
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="bi bi-search me-2"></i>Search Order
                        </button>

                        <div class="text-center">
                            <a href="<?= base_url('pos') ?>" class="text-decoration-none">
                                <i class="bi bi-house me-1"></i>Back to Dashboard
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <h6 class="text-muted mb-3">Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('pos/orders?status=pending') ?>" class="btn btn-outline-warning">
                                <i class="bi bi-clock-history me-2"></i>View Pending Orders
                            </a>
                            <a href="<?= base_url('pos/orders') ?>" class="btn btn-outline-primary">
                                <i class="bi bi-list-ul me-2"></i>View All Orders
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        Logged in as: <strong><?= esc(session()->get('name')) ?></strong>
                        (<?= ucfirst(session()->get('role')) ?>)
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-submit on barcode scan (when Enter is pressed)
        document.getElementById('order_number').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.target.form.submit();
            }
        });

        // Auto-focus on the input field
        window.addEventListener('load', function() {
            document.getElementById('order_number').focus();
        });
    </script>
</body>
</html>
