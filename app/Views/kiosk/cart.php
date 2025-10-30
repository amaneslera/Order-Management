<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Coffee Kiosk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .cart-header {
            background: linear-gradient(135deg, #8B5A3C 0%, #6B4423 100%);
            color: white;
            padding: 20px 0;
        }
        .cart-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn-checkout {
            background: #28a745;
            border: none;
            padding: 15px;
            font-size: 1.2rem;
            font-weight: 600;
        }
        .btn-checkout:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="cart-header">
        <div class="container">
            <h2><i class="bi bi-cart3 me-3"></i>Your Cart</h2>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-8">
                <?php if (empty($cart_items)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x" style="font-size: 5rem; color: #ccc;"></i>
                        <h3 class="mt-3">Your cart is empty</h3>
                        <a href="<?= base_url('kiosk') ?>" class="btn btn-primary mt-3">Browse Menu</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($cart_items as $key => $item): ?>
                    <div class="cart-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="/uploads/menu/<?= esc($item['image']) ?>" alt="<?= esc($item['name']) ?>" 
                                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px;" class="me-3">
                                <?php else: ?>
                                    <div style="width: 80px; height: 80px; background: #e0e0e0; border-radius: 10px;" class="me-3 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-cup-hot text-muted" style="font-size: 2rem;"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h5 class="mb-1"><?= esc($item['name']) ?></h5>
                                    <p class="text-muted mb-1 small">₱<?= number_format($item['price'], 2) ?> each</p>
                                    <?php if (!empty($item['addons'])): ?>
                                        <small class="text-secondary">Add-ons: <?= esc($item['addons']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="btn-group mb-2" role="group">
                                    <button class="btn btn-outline-secondary btn-sm" onclick="updateQuantity('<?= $key ?>', -1)">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                        <?= $item['quantity'] ?>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="updateQuantity('<?= $key ?>', 1)">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <div>
                                    <strong class="text-primary">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                                </div>
                                <button class="btn btn-sm btn-outline-danger mt-2" onclick="removeItem('<?= $key ?>')">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($cart_items)): ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items (<?= count($cart_items) ?>)</span>
                            <span>₱<?= number_format($total, 2) ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total</strong>
                            <strong class="text-primary h4">₱<?= number_format($total, 2) ?></strong>
                        </div>
                        
                        <form action="<?= base_url('kiosk/checkout') ?>" method="POST">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success btn-checkout w-100">
                                <i class="bi bi-check-circle me-2"></i>Proceed to Checkout
                            </button>
                        </form>

                        <a href="<?= base_url('kiosk') ?>" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                        </a>

                        <a href="<?= base_url('kiosk/cart/clear') ?>" class="btn btn-outline-danger w-100 mt-2">
                            <i class="bi bi-trash me-2"></i>Clear Cart
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateQuantity(cartKey, change) {
            const currentQty = parseInt(event.target.parentElement.querySelector('button[disabled]').textContent);
            const newQty = currentQty + change;
            
            if (newQty <= 0) {
                removeItem(cartKey);
                return;
            }

            fetch('<?= base_url('kiosk/cart/update') ?>', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `cart_key=${cartKey}&quantity=${newQty}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function removeItem(cartKey) {
            if (!confirm('Remove this item from cart?')) return;
            
            fetch('<?= base_url('kiosk/cart/remove') ?>', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `cart_key=${cartKey}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
