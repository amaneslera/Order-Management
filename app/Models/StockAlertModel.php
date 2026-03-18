<?php

namespace App\Models;

use CodeIgniter\Model;

class StockAlertModel extends Model
{
    protected $table            = 'stock_alerts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['menu_item_id', 'alert_type', 'current_stock', 'threshold', 'sent_sms', 'sent_email', 'created_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    // Get active alerts
    public function getActiveAlerts()
    {
        return $this->select('stock_alerts.*, menu_items.name, menu_items.category, menu_items.stock_quantity')
                    ->join('menu_items', 'menu_items.id = stock_alerts.menu_item_id')
                    ->where('stock_alerts.created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                    ->groupStart()
                        ->groupStart()
                            ->where('stock_alerts.alert_type', 'out_of_stock')
                            ->where('menu_items.stock_quantity', 0)
                        ->groupEnd()
                        ->orGroupStart()
                            ->where('stock_alerts.alert_type', 'low_stock')
                            ->where('menu_items.stock_quantity >', 0)
                            ->where('menu_items.stock_quantity <= menu_items.low_stock_threshold', null, false)
                        ->groupEnd()
                    ->groupEnd()
                    ->orderBy('stock_alerts.created_at', 'DESC')
                    ->findAll();
    }

    // Get alerts by type
    public function getAlertsByType($type)
    {
        return $this->where('alert_type', $type)
                    ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                    ->findAll();
    }

    // Check if alert already exists
    public function alertExists($itemId, $type)
    {
        return $this->where('menu_item_id', $itemId)
                    ->where('alert_type', $type)
                    ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-4 hours')))
                    ->first();
    }

    // Create alert
    public function createAlert($itemId, $type, $currentStock, $threshold)
    {
        // Check if recent alert exists (within 4 hours)
        if ($this->alertExists($itemId, $type)) {
            return false; // Alert already sent recently
        }

        return (bool) $this->insert([
            'menu_item_id' => $itemId,
            'alert_type'   => $type,
            'current_stock' => $currentStock,
            'threshold'    => $threshold,
            'sent_sms'     => 0,
            'sent_email'   => 0
        ]);
    }

    // Mark alert as SMS sent
    public function markSmsSent($alertId)
    {
        return $this->update($alertId, ['sent_sms' => 1]);
    }

    // Mark alert as email sent
    public function markEmailSent($alertId)
    {
        return $this->update($alertId, ['sent_email' => 1]);
    }

    // Get unsent SMS alerts
    public function getUnsentSmsAlerts()
    {
        return $this->select('stock_alerts.*, menu_items.name, menu_items.price')
                    ->join('menu_items', 'menu_items.id = stock_alerts.menu_item_id')
                    ->where('stock_alerts.sent_sms', 0)
                    ->orderBy('stock_alerts.created_at', 'DESC')
                    ->findAll();
    }

    // Get unsent email alerts
    public function getUnsentEmailAlerts()
    {
        return $this->select('stock_alerts.*, menu_items.name, menu_items.price')
                    ->join('menu_items', 'menu_items.id = stock_alerts.menu_item_id')
                    ->where('stock_alerts.sent_email', 0)
                    ->orderBy('stock_alerts.created_at', 'DESC')
                    ->findAll();
    }

    // Get statistics
    public function getAlertStats()
    {
        $today = date('Y-m-d');
        return [
            'total_today' => $this->where('DATE(created_at)', $today)->countAllResults(),
            'low_stock' => $this->where('DATE(created_at)', $today)->where('alert_type', 'low_stock')->countAllResults(),
            'out_of_stock' => $this->where('DATE(created_at)', $today)->where('alert_type', 'out_of_stock')->countAllResults(),
            'pending_sms' => $this->where('sent_sms', 0)->countAllResults(),
            'pending_email' => $this->where('sent_email', 0)->countAllResults(),
        ];
    }
}
