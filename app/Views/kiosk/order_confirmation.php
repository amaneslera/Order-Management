<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Coffee Kiosk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #6B4423 0%, #3E2723 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .confirmation-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .success-icon {
            font-size: 5rem;
            color: #28a745;
        }
        .barcode-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
            border: 2px dashed #6B4423;
        }
        .order-number {
            font-size: 2rem;
            font-weight: bold;
            color: #6B4423;
            letter-spacing: 2px;
        }
        .item-list {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="confirmation-card">
                    <div class="text-center">
                        <i class="bi bi-check-circle-fill success-icon"></i>
                        <h2 class="mt-3 mb-2">Order Placed Successfully!</h2>
                        <p class="text-muted">Thank you for your order</p>
                    </div>

                    <div class="barcode-container">
                        <p class="mb-2">Your Order Number</p>
                        <div class="order-number"><?= esc($order['order_number']) ?></div>
                        
                        <!-- Barcode Generation using existing barcode system -->
                        <div class="mt-3" id="barcode-image">
                            <img src="/barcode-master/generate-barcode.php?text=<?= urlencode($order['order_number']) ?>" 
                                 alt="Order Barcode" 
                                 style="max-width: 100%; height: auto;">
                        </div>
                        
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Show this code to the cashier
                        </small>
                    </div>

                    <div class="item-list">
                        <h5 class="mb-3"><i class="bi bi-receipt me-2"></i>Order Summary</h5>
                        <table class="table table-borderless">
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
                                    <td class="text-end">x<?= $item['quantity'] ?></td>
                                    <td class="text-end">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="border-top">
                                    <td colspan="2"><strong>Total Amount</strong></td>
                                    <td class="text-end"><h4 class="mb-0 text-primary">₱<?= number_format($order['total_amount'], 2) ?></h4></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-clock-history me-2"></i>
                        <strong>What's next?</strong>
                        <p class="mb-0 mt-2">
                            1. Please proceed to the cashier counter<br>
                            2. Show your order number or barcode<br>
                            3. Make your payment<br>
                            4. Wait for your order to be prepared
                        </p>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <a href="/kiosk" class="btn btn-primary flex-fill">
                            <i class="bi bi-house-fill me-2"></i>Back to Menu
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-secondary flex-fill">
                            <i class="bi bi-printer me-2"></i>Print Receipt
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            Order Time: <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style media="print">
        body {
            background: white;
        }
        .confirmation-card {
            box-shadow: none;
        }
        .btn, .alert {
            display: none;
        }
    </style>
</body>
</html>
