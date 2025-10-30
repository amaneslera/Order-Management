<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Kiosk - Order Now</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #6B4423;
            --secondary-color: #8B5A3C;
        }
        body {
            background: #f8f9fa;
        }
        .kiosk-header {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .menu-item-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .menu-item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .menu-item-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #e0e0e0 0%, #d0d0d0 100%);
        }
        .category-btn {
            border-radius: 25px;
            padding: 10px 25px;
            margin: 5px;
            border: 2px solid var(--primary-color);
            background: white;
            color: var(--primary-color);
            font-weight: 600;
            transition: all 0.3s;
        }
        .category-btn:hover, .category-btn.active {
            background: var(--primary-color);
            color: white;
        }
        .cart-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        .btn-add-cart {
            background: var(--secondary-color);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-add-cart:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="kiosk-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0"><i class="bi bi-cup-hot-fill me-3"></i>Coffee Kiosk</h1>
                    <p class="mb-0 small">Select your favorite items</p>
                </div>
                <div>
                    <a href="/kiosk/cart" class="btn btn-light btn-lg position-relative">
                        <i class="bi bi-cart3 me-2"></i>Cart
                        <span class="cart-badge" id="cart-count">0</span>
                    </a>
                    <a href="/login" class="btn btn-outline-light btn-lg ms-2">
                        <i class="bi bi-person-circle me-2"></i>Staff Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Categories -->
        <div class="text-center mb-4">
            <button class="category-btn active" data-category="all">All Items</button>
            <?php 
            $uniqueCategories = [];
            foreach ($categories as $cat): 
                if (!in_array($cat['category'], $uniqueCategories)):
                    $uniqueCategories[] = $cat['category'];
            ?>
                <button class="category-btn" data-category="<?= esc($cat['category']) ?>">
                    <?= esc($cat['category']) ?>
                </button>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>

        <!-- Menu Items Grid -->
        <div class="row g-4" id="menu-grid">
            <?php foreach ($menu_items as $item): ?>
            <div class="col-md-4 col-lg-3 menu-item" data-category="<?= esc($item['category']) ?>">
                <div class="card menu-item-card">
                    <div class="menu-item-image d-flex align-items-center justify-content-center">
                        <?php if ($item['image']): ?>
                            <img src="/uploads/menu/<?= esc($item['image']) ?>" alt="<?= esc($item['name']) ?>" class="menu-item-image">
                        <?php else: ?>
                            <i class="bi bi-cup-hot text-muted" style="font-size: 3rem;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title mb-1"><?= esc($item['name']) ?></h5>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-tag-fill me-1"></i><?= esc($item['category']) ?>
                        </p>
                        <?php if ($item['description']): ?>
                            <p class="card-text small text-secondary"><?= esc($item['description']) ?></p>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <h4 class="text-primary mb-0">₱<?= number_format($item['price'], 2) ?></h4>
                            <button class="btn btn-add-cart" onclick="addToCart(<?= $item['id'] ?>, '<?= esc($item['name']) ?>', <?= $item['price'] ?>)">
                                <i class="bi bi-plus-circle me-1"></i>Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Category filter
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active state
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const category = this.dataset.category;
                const items = document.querySelectorAll('.menu-item');

                items.forEach(item => {
                    if (category === 'all' || item.dataset.category === category) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Add to cart function
        function addToCart(itemId, itemName, itemPrice) {
            fetch('/kiosk/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `item_id=${itemId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount(data.cart_count);
                    showNotification(`${itemName} added to cart!`, 'success');
                } else {
                    showNotification('Failed to add item', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }

        // Update cart count
        function updateCartCount(count) {
            document.getElementById('cart-count').textContent = count;
        }

        // Show notification
        function showNotification(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

        // Load cart count on page load
        // This would need to be implemented with a server endpoint or session
    </script>
</body>
</html>
