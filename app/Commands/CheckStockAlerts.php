<?php

namespace App\Commands;

use App\Models\MenuItemModel;
use App\Models\StockAlertModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckStockAlerts extends BaseCommand
{
    protected $group       = 'Inventory';
    protected $name        = 'stock:check';
    protected $description = 'Checks low/out-of-stock items, creates alerts, and sends SMS notifications.';

    public function run(array $params)
    {
        $menuModel = new MenuItemModel();
        $stockAlertModel = new StockAlertModel();

        $items = $menuModel->findAll();
        $alertsCreated = 0;
        $smsSent = 0;

        foreach ($items as $item) {
            $stock = (int) ($item['stock_quantity'] ?? 0);
            $threshold = (int) ($item['low_stock_threshold'] ?? 0);

            $alertType = null;
            $thresholdValue = $threshold;
            if ($stock === 0) {
                $alertType = 'out_of_stock';
                $thresholdValue = 0;
            } elseif ($stock > 0 && $stock <= $threshold) {
                $alertType = 'low_stock';
            }

            if ($alertType === null) {
                continue;
            }

            $alertId = $stockAlertModel->createAlert((int) $item['id'], $alertType, $stock, $thresholdValue);
            if ($alertId === false) {
                continue;
            }

            $alertsCreated++;

            if ($this->sendStockAlertSMS($item, $alertType)) {
                $stockAlertModel->markSmsSent((int) $alertId);
                $smsSent++;
            }
        }

        CLI::write("Stock check complete. Alerts created: {$alertsCreated}; SMS sent: {$smsSent}", 'green');
    }

    private function sendStockAlertSMS(array $item, string $type): bool
    {
        $adminPhone = getenv('ADMIN_PHONE_NUMBER') ?: getenv('sms.adminPhone');
        if (!$adminPhone) {
            return false;
        }

        $message = $type === 'out_of_stock'
            ? "ALERT: {$item['name']} is OUT OF STOCK."
            : "ALERT: {$item['name']} stock is LOW ({$item['stock_quantity']} remaining).";

        // Try Twilio first
        $twilioSid = getenv('TWILIO_ACCOUNT_SID');
        $twilioToken = getenv('TWILIO_AUTH_TOKEN');
        $twilioPhone = getenv('TWILIO_PHONE_NUMBER');

        if ($twilioSid && $twilioToken && $twilioPhone) {
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$twilioSid}/Messages.json";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "{$twilioSid}:{$twilioToken}");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'From' => $twilioPhone,
                'To'   => $adminPhone,
                'Body' => $message,
            ]));
            $response = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
                return true;
            }
        }

        // Fallback: Semaphore
        $semaphoreApiKey = getenv('sms.apiKey');
        $semaphoreSender = getenv('sms.senderName') ?: 'CoffeeKiosk';
        if (!$semaphoreApiKey || $semaphoreApiKey === 'your-semaphore-api-key-here') {
            return false;
        }

        $url = 'https://semaphore.co/api/v4/messages';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'apikey' => $semaphoreApiKey,
            'number' => $adminPhone,
            'message' => $message,
            'sendername' => $semaphoreSender,
        ]));
        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $response !== false && $httpCode >= 200 && $httpCode < 300;
    }
}
