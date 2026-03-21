<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'action', 'description', 'created_at'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';

    // Log activity
    public function logActivity($userId, $action, $description = '')
    {
        return $this->insert([
            'user_id'     => $userId,
            'action'      => $action,
            'description' => $description,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    // Get logs with user info
    public function getLogsWithUsers($limit = 100)
    {
        return $this->select('activity_logs.*, users.username as user_name, users.role')
                    ->join('users', 'users.id = activity_logs.user_id', 'left')
                    ->orderBy('activity_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    // Get logs with optional filters and user info
    public function getFilteredLogsWithUsers(array $filters = [], int $limit = 100): array
    {
        $builder = $this->select('activity_logs.*, users.username as user_name, users.role')
            ->join('users', 'users.id = activity_logs.user_id', 'left');

        $action = trim((string) ($filters['action'] ?? ''));
        if ($action !== '') {
            $builder->where('activity_logs.action', $action);
        }

        $role = strtolower(trim((string) ($filters['role'] ?? '')));
        if ($role !== '') {
            if ($role === 'admin') {
                $builder->groupStart()
                    ->where('users.role', 'Admin')
                    ->orWhere('users.role', 'admin')
                    ->groupEnd();
            } elseif ($role === 'cashier') {
                $builder->groupStart()
                    ->where('users.role', 'cashier')
                    ->orWhere('users.role', 'Cashier')
                    ->groupEnd();
            }
        }

        $dateFrom = trim((string) ($filters['date_from'] ?? ''));
        if ($dateFrom !== '') {
            $builder->where('activity_logs.created_at >=', $dateFrom . ' 00:00:00');
        }

        $dateTo = trim((string) ($filters['date_to'] ?? ''));
        if ($dateTo !== '') {
            $builder->where('activity_logs.created_at <=', $dateTo . ' 23:59:59');
        }

        return $builder->orderBy('activity_logs.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    // Get logs by date range
    public function getLogsByDateRange($startDate, $endDate)
    {
        // Ensure end date includes the entire day (23:59:59)
        $endDateTime = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
        
        return $this->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDateTime)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
