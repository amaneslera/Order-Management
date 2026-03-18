<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Step - POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #6B4423;
            --primary-dark: #3E2723;
            --surface-muted: #f8f6f3;
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
        .panel-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(16, 24, 40, 0.05);
            overflow: hidden;
            background: #fff;
        }
        .panel-card .card-header {
            background: var(--surface-muted);
            border-bottom: 1px solid #eceef1;
            font-weight: 700;
            color: #2a2a2a;
            padding: 14px 16px;
        }
        .panel-subtext {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 4px;
        }
        .order-table thead th {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            color: #5b6470;
            background: #fbfcfd;
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
        .receipt-step {
            display: none;
        }
        .receipt-step.show {
            display: block;
        }
        .countdown-text {
            color: #5a6572;
            font-size: 0.92rem;
        }
        .receipt-frame {
            width: 100%;
            height: 62vh;
            border: 1px solid #e2e6ea;
            border-radius: 10px;
            background: #fff;
        }
        .status-pill {
            display: inline-block;
            border-radius: 999px;
            font-size: 0.88rem;
            padding: 6px 14px;
            font-weight: 700;
        }
        .status-pill.pending {
            background: #f4c430;
            color: #472d00;
        }
        .status-pill.paid {
            background: #198754;
            color: #fff;
        }
        @media (max-width: 991px) {
            .order-header {
                padding: 22px;
            }
            .receipt-frame {
                height: 56vh;
            }
        }
    </style>
</head>
<body>
    <div id="feedback-stack" class="feedback-stack" aria-live="polite" aria-atomic="true"></div>
    <?php $isPending = (($order['status'] ?? '') === 'pending'); ?>
    <div class="container py-4">
        <div class="mb-3">
            <a href="<?= base_url('pos/order/' . $order['id']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Order
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
                    <span class="status-pill <?= $isPending ? 'pending' : 'paid' ?>"><?= $isPending ? 'Pending' : 'Paid' ?></span>
                    <h2 class="mt-2 mb-0">₱<?= number_format((float) $order['total_amount'], 2) ?></h2>
                </div>
            </div>
        </div>

        <div class="row g-4" id="payment-step"<?= $isPending ? '' : ' style="display:none;"' ?>>
            <div class="col-md-8">
                <div class="card panel-card">
                    <div class="card-header">
                        <div><i class="bi bi-list-ul me-2"></i>Order Items</div>
                        <div class="panel-subtext">Read-only list for final payment confirmation.</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle order-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($order['items'] ?? []) as $item): ?>
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
                                        <td>₱<?= number_format((float) $item['price'], 2) ?></td>
                                        <td class="text-center"><?= (int) $item['quantity'] ?></td>
                                        <td class="text-end">₱<?= number_format((float) $item['price'] * (int) $item['quantity'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="border-top">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td class="text-end"><strong>₱<?= number_format((float) $order['total_amount'], 2) ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card panel-card">
                    <div class="card-header">
                        <div><i class="bi bi-cash-coin me-2"></i>Payment</div>
                        <div class="panel-subtext">Enter amount received and press Enter.</div>
                    </div>
                    <div class="card-body">
                        <div class="summary-row">
                            <span class="summary-label">Order Total</span>
                            <span class="summary-value" id="order-total">₱<?= number_format((float) $order['total_amount'], 2) ?></span>
                        </div>

                        <form id="paymentForm" class="mt-3">
                            <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                            <input type="hidden" name="payment_method" value="cash">

                            <div class="mb-3">
                                <label class="form-label">Amount Received</label>
                                <input type="number" class="form-control form-control-lg" id="amount-received" name="amount" step="0.01" min="0" value="<?= number_format((float) $order['total_amount'], 2, '.', '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Change</label>
                                <input type="text" class="form-control" id="change-display" value="₱0.00" readonly>
                            </div>

                            <button type="submit" id="enter-payment-btn" class="btn btn-success w-100">
                                <i class="bi bi-check-circle me-2"></i>Enter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="receipt-step" class="receipt-step<?= $isPending ? '' : ' show' ?>">
            <div class="card panel-card">
                <div class="card-header">
                    <div><i class="bi bi-receipt me-2"></i>Receipt</div>
                    <div class="panel-subtext">Payment recorded. This page returns to dashboard automatically.</div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div id="countdown-text" class="countdown-text">Redirecting to dashboard in 10 seconds...</div>
                        <a href="<?= base_url('pos') ?>" class="btn btn-outline-secondary btn-sm">Go Now</a>
                    </div>
                    <iframe id="receipt-frame" class="receipt-frame" src="<?= base_url('pos/receipt/' . $order['id']) ?>"></iframe>
                </div>
            </div>
        </div>

        <?php if (!$isPending): ?>
            <script>
                window.addEventListener('DOMContentLoaded', function() {
                    var paymentStep = document.getElementById('payment-step');
                    if (paymentStep) paymentStep.style.display = 'none';
                });
            </script>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const orderTotal = Number(<?= json_encode((float) $order['total_amount']) ?>);
        const isPending = <?= $isPending ? 'true' : 'false' ?>;

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
            }, 3500);
        }

        function formatPeso(value) {
            return `₱${Number(value).toFixed(2)}`;
        }

        function updateChangeDisplay() {
            const amountInput = document.getElementById('amount-received');
            const changeDisplay = document.getElementById('change-display');
            const enterBtn = document.getElementById('enter-payment-btn');
            if (!amountInput || !changeDisplay || !enterBtn) return;

            const received = Number(amountInput.value || 0);
            const change = received - orderTotal;
            changeDisplay.value = formatPeso(Math.max(0, change));
            enterBtn.disabled = received < orderTotal;
        }

        function startDashboardCountdown() {
            let seconds = 10;
            const countdownText = document.getElementById('countdown-text');
            if (!countdownText) return;

            countdownText.textContent = `Redirecting to dashboard in ${seconds} seconds...`;

            const timer = setInterval(() => {
                seconds -= 1;
                if (seconds <= 0) {
                    clearInterval(timer);
                    window.location.href = '<?= base_url('pos') ?>';
                    return;
                }
                countdownText.textContent = `Redirecting to dashboard in ${seconds} seconds...`;
            }, 1000);
        }

        if (isPending) {
            updateChangeDisplay();

            const amountInput = document.getElementById('amount-received');
            if (amountInput) {
                amountInput.addEventListener('input', updateChangeDisplay);
                amountInput.focus();
                amountInput.select();
            }

            const paymentForm = document.getElementById('paymentForm');
            if (paymentForm) {
                paymentForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const amountReceived = Number(document.getElementById('amount-received').value || 0);
                    if (amountReceived < orderTotal) {
                        showFeedback('Amount received is less than order total.', 'error');
                        return;
                    }

                    const formData = new FormData(paymentForm);
                    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                    fetch('<?= base_url('pos/payment/process') ?>', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new URLSearchParams(formData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            showFeedback('Failed to process payment: ' + (data.message || ''), 'error');
                            return;
                        }

                        const paymentStep = document.getElementById('payment-step');
                        const receiptStep = document.getElementById('receipt-step');
                        if (paymentStep) paymentStep.style.display = 'none';
                        if (receiptStep) receiptStep.classList.add('show');

                        showFeedback('Payment processed successfully.', 'success');
                        startDashboardCountdown();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showFeedback('An error occurred while processing payment.', 'error');
                    });
                });
            }
        } else {
            startDashboardCountdown();
        }
    </script>
</body>
</html>