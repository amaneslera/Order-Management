<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary: #6B4423; }
        .order-header {
            background: linear-gradient(135deg, var(--primary) 0%, #3E2723 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
        }
        .status-badge {
            font-size: 1rem;
            padding: 8px 20px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="mb-3">
            <a href="/pos" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to POS
            </a>
        </div>

        <div class="order-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="mb-2">Order #<?= esc($order['order_number']) ?></h3>
                    <p class="mb-0">
                        <i class="bi bi-clock me-2"></i>
                        <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge status-badge bg-<?= $order['status'] === 'pending' ? 'warning' : ($order['status'] === 'paid' ? 'success' : 'info') ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                    <h2 class="mt-2 mb-0">₱<?= number_format($order['total_amount'], 2) ?></h2>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Order Items -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($item['name']) ?></strong>
                                            <?php if (!empty($item['addons'])): ?>
                                                <br><small class="text-muted">Add-ons: <?= esc($item['addons']) ?></small>
                                            <?php endif; ?>
                                            <?php if (!empty($item['notes'])): ?>
                                                <br><small class="text-muted">Notes: <?= esc($item['notes']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>₱<?= number_format($item['price'], 2) ?></td>
                                        <td class="text-center"><?= $item['quantity'] ?></td>
                                        <td class="text-end">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                        <td class="text-end">
                                            <?php if ($order['status'] === 'pending'): ?>
                                            <button class="btn btn-sm btn-outline-danger" onclick="removeItem(<?= $item['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="border-top">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td class="text-end"><h5 class="mb-0 text-primary">₱<?= number_format($order['total_amount'], 2) ?></h5></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <?php if ($order['status'] === 'pending'): ?>
                        <div class="mt-3">
                            <h6>Add Item to Order</h6>
                            <form id="addItemForm" class="row g-2">
                                <div class="col-md-6">
                                    <select class="form-select" name="menu_item_id" required>
                                        <option value="">Select Item</option>
                                        <?php foreach ($menu_items as $menuItem): ?>
                                            <option value="<?= $menuItem['id'] ?>"><?= esc($menuItem['name']) ?> - ₱<?= number_format($menuItem['price'], 2) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" name="quantity" min="1" value="1" placeholder="Qty" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-plus-circle me-2"></i>Add Item
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Barcode -->
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Order Barcode</h6>
                        <img src="/barcode-master/generate-barcode.php?text=<?= urlencode($order['order_number']) ?>" 
                             alt="Order Barcode" style="max-width: 100%; height: auto;">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="col-md-4">
                <!-- Status Update -->
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Order Status</h6>
                    </div>
                    <div class="card-body">
                        <form id="statusForm">
                            <select class="form-select mb-3" name="status" id="orderStatus">
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="paid" <?= $order['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-2"></i>Update Status
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Payment -->
                <?php if ($order['status'] === 'pending'): ?>
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Process Payment</h6>
                    </div>
                    <div class="card-body">
                        <form id="paymentForm">
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="cash">Cash</option>
                                    <option value="gcash">GCash</option>
                                    <option value="card">Credit/Debit Card</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Amount</label>
                                <input type="number" class="form-control" name="amount" step="0.01" 
                                       value="<?= $order['total_amount'] ?>" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-cash-coin me-2"></i>Process Payment
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Actions</h6>
                    </div>
                    <div class="card-body">
                        <a href="/pos/receipt/<?= $order['id'] ?>" class="btn btn-outline-primary w-100 mb-2" target="_blank">
                            <i class="bi bi-printer me-2"></i>Print Receipt
                        </a>
                        <a href="/pos" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update status
        document.getElementById('statusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const status = document.getElementById('orderStatus').value;
            
            fetch('/pos/order/status/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `order_id=<?= $order['id'] ?>&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Status updated successfully!');
                    location.reload();
                } else {
                    alert('Failed to update status');
                }
            });
        });

        // Process payment
        <?php if ($order['status'] === 'pending'): ?>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('order_id', '<?= $order['id'] ?>');
            
            fetch('/pos/payment/process', {
                method: 'POST',
                body: new URLSearchParams(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment processed successfully!');
                    location.reload();
                } else {
                    alert('Failed to process payment');
                }
            });
        });

        // Add item
        document.getElementById('addItemForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('order_id', '<?= $order['id'] ?>');
            
            fetch('/pos/order/item/add', {
                method: 'POST',
                body: new URLSearchParams(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Item added successfully!');
                    location.reload();
                } else {
                    alert('Failed to add item');
                }
            });
        });
        <?php endif; ?>

        // Remove item
        function removeItem(itemId) {
            if (!confirm('Remove this item?')) return;
            
            fetch('/pos/order/item/remove', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `item_id=${itemId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Item removed!');
                    location.reload();
                } else {
                    alert('Failed to remove item');
                }
            });
        }
    </script>
</body>
</html>
