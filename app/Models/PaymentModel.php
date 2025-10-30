<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['order_id', 'payment_method', 'amount', 'payment_date'];

    // Validation
    protected $validationRules      = [
        'order_id'       => 'required|integer',
        'payment_method' => 'required',
        'amount'         => 'required|decimal',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Get payment by order
    public function getPaymentByOrder($orderId)
    {
        return $this->where('order_id', $orderId)->first();
    }

    // Get payments by date range
    public function getPaymentsByDateRange($startDate, $endDate)
    {
        return $this->where('payment_date >=', $startDate)
                    ->where('payment_date <=', $endDate)
                    ->findAll();
    }

    // Get payment methods summary
    public function getPaymentMethodsSummary($startDate = null, $endDate = null)
    {
        $builder = $this->select('payment_method, COUNT(*) as count, SUM(amount) as total')
                        ->groupBy('payment_method');

        if ($startDate && $endDate) {
            $builder->where('payment_date >=', $startDate)
                    ->where('payment_date <=', $endDate);
        }

        return $builder->findAll();
    }
}
