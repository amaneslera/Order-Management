<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?= esc($order['order_number']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .receipt-wrap {
            max-width: 760px;
            margin: 24px auto;
            background: #fff;
            border: 1px solid #e6e6e6;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .receipt-header {
            background: #6B4423;
            color: #fff;
            padding: 20px;
        }
        .receipt-body {
            padding: 20px;
        }
        .meta td {
            padding: 3px 0;
            vertical-align: top;
        }
        .summary-row td {
            font-weight: bold;
            border-top: 2px solid #222;
        }
        @media print {
            body {
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
            .receipt-wrap {
                margin: 0;
                max-width: 100%;
                border: 0;
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-wrap">
        <div class="receipt-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Coffee Kiosk Receipt</h4>
                <div class="small">Order #<?= esc($order['order_number']) ?></div>
            </div>
            <div class="text-end">
                <div class="small">Status</div>
                <strong><?= strtoupper(esc($order['status'])) ?></strong>
            </div>
        </div>

        <div class="receipt-body">
            <div class="row mb-3">
                <div class="col-md-7">
                    <table class="meta">
                        <tr>
                            <td style="width:130px;">Order Date</td>
                            <td>: <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <td>Payment Date</td>
                            <td>: <?= !empty($payment['payment_date']) ? date('M d, Y h:i A', strtotime($payment['payment_date'])) : 'N/A' ?></td>
                        </tr>
                        <tr>
                            <td>Payment Method</td>
                            <td>: <?= !empty($payment['payment_method']) ? strtoupper(esc($payment['payment_method'])) : 'UNPAID' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-5 text-md-end">
                    <img src="<?= base_url('barcode-master/generate-barcode.php?text=' . urlencode($order['order_number'])) ?>" alt="Order Barcode" style="max-width: 240px; width: 100%; height: auto;">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-end">Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($order['items'] ?? []) as $item): ?>
                            <tr>
                                <td>
                                    <?= esc($item['name']) ?>
                                    <?php if (!empty($item['addons'])): ?>
                                        <div class="small text-muted">Add-ons: <?= esc($item['addons']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['notes'])): ?>
                                        <div class="small text-muted">Notes: <?= esc($item['notes']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">PHP <?= number_format((float) $item['price'], 2) ?></td>
                                <td class="text-center"><?= (int) $item['quantity'] ?></td>
                                <td class="text-end">PHP <?= number_format((float) $item['price'] * (int) $item['quantity'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="summary-row">
                            <td colspan="3" class="text-end">Total</td>
                            <td class="text-end">PHP <?= number_format((float) $order['total_amount'], 2) ?></td>
                        </tr>
                        <?php if (!empty($payment['amount'])): ?>
                            <tr>
                                <td colspan="3" class="text-end">Amount Paid</td>
                                <td class="text-end">PHP <?= number_format((float) $payment['amount'], 2) ?></td>
                            </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>

            <div class="text-center mt-4 small text-muted">
                Thank you for your order.
            </div>
        </div>
    </div>

    <div class="no-print text-center mb-4">
        <button class="btn btn-primary" onclick="window.print()">Print Receipt</button>
        <a href="<?= base_url('pos/order/' . $order['id']) ?>" class="btn btn-outline-secondary ms-2">Back to Order</a>
    </div>
</body>
</html>
