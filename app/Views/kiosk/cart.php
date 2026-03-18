<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Coffee Kiosk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --kiosk-primary: #6B4423;
            --kiosk-secondary: #8B5A3C;
            --kiosk-cream: #f8f3ed;
            --kiosk-muted: #8a8179;
        }
        body { background: #f4f5f7; }
        .cart-header {
            background: linear-gradient(135deg, var(--kiosk-secondary) 0%, var(--kiosk-primary) 100%);
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
        .empty-cart-wrap {
            min-height: 58vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 0;
        }
        .empty-cart-card {
            width: min(100%, 700px);
            background: linear-gradient(165deg, #ffffff 0%, #fdf9f5 100%);
            border: 1px solid #efe5d8;
            border-radius: 22px;
            box-shadow: 0 16px 40px rgba(107, 68, 35, 0.12);
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .empty-cart-icon {
            width: 92px;
            height: 92px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at 30% 30%, #ffffff, #f2e7da);
            border: 1px solid #ead8c4;
            color: var(--kiosk-primary);
            font-size: 2.3rem;
        }
        .empty-cart-title {
            margin-bottom: 0.4rem;
            font-weight: 700;
            color: #2f2a25;
        }
        .empty-cart-text {
            color: var(--kiosk-muted);
            max-width: 460px;
            margin: 0 auto 1.25rem;
        }
        .empty-cart-hints {
            display: inline-flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .empty-cart-chip {
            border: 1px solid #e8d9c8;
            background: var(--kiosk-cream);
            color: #5b5147;
            border-radius: 999px;
            font-size: 0.82rem;
            padding: 0.35rem 0.75rem;
            font-weight: 600;
        }
        .btn-return-menu {
            background: linear-gradient(135deg, var(--kiosk-secondary), var(--kiosk-primary));
            border: none;
            color: #fff;
            border-radius: 999px;
            padding: 0.7rem 1.5rem;
            font-weight: 600;
            box-shadow: 0 8px 20px rgba(107, 68, 35, 0.28);
        }
        .btn-return-menu:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(107, 68, 35, 0.3);
        }
        @media (max-width: 576px) {
            .empty-cart-card {
                padding: 2rem 1.2rem;
                border-radius: 18px;
            }
            .empty-cart-wrap {
                min-height: 48vh;
            }
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
            <?php if (empty($cart_items)): ?>
                <div class="col-12">
                    <div class="empty-cart-wrap">
                        <div class="empty-cart-card">
                            <div class="empty-cart-icon">
                                <i class="bi bi-cart-x"></i>
                            </div>
                            <h3 class="empty-cart-title">Your cart is empty</h3>
                            <p class="empty-cart-text">Looks like you have not picked anything yet. Explore the menu and add your favorite coffee or snack.</p>
                            <div class="empty-cart-hints">
                                <span class="empty-cart-chip">Fresh Espresso</span>
                                <span class="empty-cart-chip">Iced Latte</span>
                                <span class="empty-cart-chip">Pastry Pairings</span>
                            </div>
                            <a href="<?= base_url('kiosk') ?>" class="btn btn-return-menu">
                                <i class="bi bi-arrow-left me-2"></i>Return to Menu
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-md-8">
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
                                    <button class="btn btn-outline-secondary btn-sm" onclick="updateQuantity('<?= $key ?>', -1, <?= (int) $item['quantity'] ?>, <?= (int) ($item['line_max_quantity'] ?? 0) ?>)">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                        <?= $item['quantity'] ?>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="updateQuantity('<?= $key ?>', 1, <?= (int) $item['quantity'] ?>, <?= (int) ($item['line_max_quantity'] ?? 0) ?>)" <?= ((int) $item['quantity'] >= (int) ($item['line_max_quantity'] ?? 0)) ? 'disabled title="Maximum stock reached"' : '' ?>>
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <div>
                                    <strong class="text-primary">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                                </div>
                                <div class="small text-muted">Stock available: <?= (int) ($item['available_stock'] ?? 0) ?></div>
                                <button class="btn btn-sm btn-outline-danger mt-2" onclick="removeItem('<?= $key ?>')">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

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
                                <i class="bi bi-arrow-left me-2"></i>Return to Menu
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
        function updateQuantity(cartKey, change, currentQty, maxStock) {
            if (change > 0 && currentQty >= maxStock) {
                alert(`Only ${maxStock} item(s) available in stock.`);
                return;
            }

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
                } else if (data.message) {
                    alert(data.message);
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
