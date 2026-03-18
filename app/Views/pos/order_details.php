<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #6B4423;
            --primary-dark: #3E2723;
            --surface: #ffffff;
            --surface-muted: #f8f6f3;
            --text-muted: #6c757d;
        }
        body {
            background:
                radial-gradient(circle at 0% 0%, rgba(107, 68, 35, 0.05), transparent 35%),
                #f3f4f6;
        }
        .order-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(42, 29, 20, 0.22);
        }
        .status-badge {
            font-size: 1rem;
            padding: 8px 20px;
            border-radius: 999px;
        }
        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.26);
            background: rgba(255, 255, 255, 0.14);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 0.88rem;
            margin-top: 8px;
            margin-right: 6px;
        }
        .panel-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(16, 24, 40, 0.05);
            overflow: hidden;
        }
        .panel-card .card-header {
            background: var(--surface-muted);
            border-bottom: 1px solid #eceef1;
            font-weight: 700;
            color: #2a2a2a;
            padding: 14px 16px;
        }
        .panel-subtext {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-top: 4px;
        }
        .order-table thead th {
            font-size: 0.84rem;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            color: #5b6470;
            background: #fbfcfd;
        }
        .order-table tbody tr:hover {
            background: #faf9f7;
        }
        .step-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #e8eefc;
            color: #2457c5;
            font-size: 0.82rem;
            font-weight: 700;
            margin-right: 8px;
        }
        .sticky-panel {
            position: sticky;
            top: 16px;
        }
        .feedback-stack {
            position: fixed;
            top: 14px;
            right: 14px;
            z-index: 1080;
            width: min(360px, calc(100vw - 24px));
        }
        .feedback-stack .alert {
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
            margin-bottom: 10px;
        }
        .section-spacer {
            margin-top: 1rem;
        }
        .qty-editor {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .qty-editor .form-control {
            width: 70px;
            text-align: center;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #e8eaee;
        }
        .summary-row:last-child {
            border-bottom: none;
        }
        .summary-label {
            color: #5f6672;
        }
        .summary-value {
            font-weight: 700;
            color: #20252b;
        }
        @media (max-width: 991px) {
            .sticky-panel {
                position: static;
            }
            .order-header {
                padding: 22px;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div id="feedback-stack" class="feedback-stack" aria-live="polite" aria-atomic="true"></div>
    <?php $totalItems = array_sum(array_map(static fn($line) => (int) ($line['quantity'] ?? 0), $order['items'])); ?>
    <?php $isPending = ($order['status'] === 'pending'); ?>
    <?php $isPaid = ($order['status'] === 'paid'); ?>
    <div class="container py-4">
        <div class="mb-3">
            <a href="<?= base_url('pos') ?>" class="btn btn-outline-secondary">
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
                    <div>
                        <span class="meta-pill"><i class="bi bi-bag-check"></i><?= $totalItems ?> items</span>
                        <span class="meta-pill" id="header-flow-pill"><i class="bi bi-receipt-cutoff"></i><?= $isPending ? 'Step 1: Review order' : 'Ready for receipt' ?></span>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <span id="order-status-badge" class="badge status-badge bg-<?= $order['status'] === 'pending' ? 'warning' : ($order['status'] === 'paid' ? 'success' : 'info') ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                    <h2 class="mt-2 mb-0">₱<?= number_format($order['total_amount'], 2) ?></h2>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Order Items -->
            <div class="col-md-8">
                <div class="card panel-card mb-4">
                    <div class="card-header">
                        <div><i class="bi bi-list-ul me-2"></i>Order Items</div>
                        <div class="panel-subtext">Review items before changing status or processing payment.</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle order-table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th class="text-center">Qty<?= $isPending ? ' (editable)' : '' ?></th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-end"><?= $isPending ? 'Actions' : '' ?></th>
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
                                        <td class="text-center">
                                            <?php if ($isPending): ?>
                                                <div class="qty-editor justify-content-center">
                                                    <button class="btn btn-sm btn-outline-secondary pending-edit-control" onclick="adjustQty(<?= (int) $item['id'] ?>, -1)">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <input id="qty-input-<?= (int) $item['id'] ?>" type="number" class="form-control form-control-sm pending-edit-control" value="<?= (int) $item['quantity'] ?>" min="1" oninput="scheduleQuantityUpdate(<?= (int) $item['id'] ?>)" onblur="updateItemQuantity(<?= (int) $item['id'] ?>)">
                                                    <button class="btn btn-sm btn-outline-secondary pending-edit-control" onclick="adjustQty(<?= (int) $item['id'] ?>, 1)">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <span class="fw-semibold"><?= (int) $item['quantity'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                        <td class="text-end">
                                            <?php if ($isPending): ?>
                                            <button class="btn btn-sm btn-outline-danger pending-edit-control" onclick="removeItem(<?= (int) $item['id'] ?>)">
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

                        <?php if ($isPending): ?>
                        <div id="add-item-section" class="mt-3 section-spacer">
                            <h6 class="mb-1">Add Item to Order</h6>
                            <p class="panel-subtext mb-2">Use this if the customer wants to add more before payment.</p>
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
                <div class="card panel-card">
                    <div class="card-body text-center">
                        <h6 class="mb-1">Order Barcode</h6>
                        <p class="panel-subtext">Scan this barcode to quickly retrieve the order in POS.</p>
                        <img src="<?= base_url('barcode-master/generate-barcode.php?text=' . urlencode($order['order_number'])) ?>" 
                             alt="Order Barcode" style="max-width: 100%; height: auto;">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="col-md-4">
                <div class="sticky-panel">
                <div class="card panel-card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><span class="step-label">1</span>Order Summary</h6>
                        <div class="panel-subtext">Confirm order details before final action.</div>
                    </div>
                    <div class="card-body">
                        <div class="summary-row">
                            <span class="summary-label">Status</span>
                            <span class="summary-value" id="summary-status-text"><?= ucfirst($order['status']) ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Total Items</span>
                            <span class="summary-value"><?= (int) $totalItems ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Order Total</span>
                            <span class="summary-value" id="order-total-text">₱<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>

                <?php if ($isPending): ?>
                <div id="pending-action-card" class="card panel-card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><span class="step-label">2</span>Actions</h6>
                        <div class="panel-subtext">Select what to do next.</div>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2 mb-3">
                            <button id="cancel-order-btn" type="button" class="btn btn-outline-danger" onclick="cancelOrder()">
                                <i class="bi bi-x-circle me-2"></i>Cancel Order
                            </button>
                            <a href="<?= base_url('pos/payment/' . $order['id']) ?>" class="btn btn-success">
                                <i class="bi bi-cash-coin me-2"></i>Process Payment
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="card panel-card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><span class="step-label">2</span>Payment Details</h6>
                        <div class="panel-subtext">Review saved payment information.</div>
                    </div>
                    <div class="card-body">
                        <div class="summary-row">
                            <span class="summary-label">Payment Method</span>
                            <span class="summary-value"><?= esc(ucfirst($payment['payment_method'] ?? 'N/A')) ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Amount</span>
                            <span class="summary-value">₱<?= number_format((float) ($payment['amount'] ?? $order['total_amount']), 2) ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Paid At</span>
                            <span class="summary-value"><?= !empty($payment['payment_date']) ? date('M d, Y h:i A', strtotime($payment['payment_date'])) : 'N/A' ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const qtyUpdateTimers = {};

        function showFeedback(message, type = 'success') {
            const stack = document.getElementById('feedback-stack');
            const alert = document.createElement('div');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            alert.className = `alert ${alertClass} alert-dismissible fade show`;
            alert.role = 'alert';
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            stack.appendChild(alert);

            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 3200);
        }

        function formatPeso(value) {
            return `₱${Number(value).toFixed(2)}`;
        }

        function adjustQty(itemId, delta) {
            const input = document.getElementById(`qty-input-${itemId}`);
            if (!input) return;
            const currentValue = Number(input.value || 1);
            const nextValue = Math.max(1, currentValue + delta);
            input.value = nextValue;
            updateItemQuantity(itemId);
        }

        function scheduleQuantityUpdate(itemId) {
            if (qtyUpdateTimers[itemId]) {
                clearTimeout(qtyUpdateTimers[itemId]);
            }

            qtyUpdateTimers[itemId] = setTimeout(() => {
                updateItemQuantity(itemId);
            }, 450);
        }

        function updateItemQuantity(itemId) {
            const input = document.getElementById(`qty-input-${itemId}`);
            if (!input) return;

            const qty = Number(input.value || 0);
            if (qty < 1) {
                showFeedback('Quantity must be at least 1.', 'error');
                return;
            }

            if (qtyUpdateTimers[itemId]) {
                clearTimeout(qtyUpdateTimers[itemId]);
                qtyUpdateTimers[itemId] = null;
            }

            fetch('<?= base_url('pos/order/item/update') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `item_id=${itemId}&quantity=${qty}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFeedback('Quantity updated successfully.', 'success');
                    location.reload();
                } else {
                    showFeedback('Failed to update quantity: ' + (data.message || ''), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFeedback('An error occurred while updating quantity.', 'error');
            });
        }

        function cancelOrder() {
            if (!confirm('Cancel this order? This action cannot be undone.')) return;

            fetch('<?= base_url('pos/order/status/update') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `order_id=<?= $order['id'] ?>&status=cancelled&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFeedback('Order cancelled successfully.', 'success');
                    setTimeout(() => {
                        window.location.href = '<?= base_url('pos') ?>';
                    }, 400);
                } else {
                    showFeedback('Failed to cancel order: ' + (data.message || ''), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFeedback('An error occurred while cancelling the order.', 'error');
            });
        }

        <?php if ($isPending): ?>
        document.getElementById('addItemForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('order_id', '<?= $order['id'] ?>');
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            
            fetch('<?= base_url('pos/order/item/add') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFeedback('Item added successfully!', 'success');
                    location.reload();
                } else {
                    showFeedback('Failed to add item: ' + (data.message || ''), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFeedback('An error occurred while adding item', 'error');
            });
        });
        <?php endif; ?>

        function removeItem(itemId) {
            if (!confirm('Remove this item?')) return;
            
            fetch('<?= base_url('pos/order/item/remove') ?>', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `item_id=${itemId}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFeedback('Item removed successfully.', 'success');
                    location.reload();
                } else {
                    showFeedback('Failed to remove item: ' + (data.message || ''), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFeedback('An error occurred while removing item', 'error');
            });
        }
    </script>
</body>
</html>
