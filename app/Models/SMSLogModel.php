<?php

namespace App\Models;

use CodeIgniter\Model;

class SMSLogModel extends Model
{
    protected $table            = 'staff_sms_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'staff_id', 
        'staff_name', 
        'message', 
        'admin_phone', 
        'status', 
        'error_message', 
        'sms_id', 
        'sent_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation
    protected $validationRules = [
        'staff_id'   => 'required|integer',
        'staff_name' => 'required|max_length[100]',
        'message'    => 'required',
        'admin_phone' => 'required|max_length[20]',
        'status'     => 'required|in_list[SENT,FAILED]',
    ];

    protected $validationMessages = [
        'staff_id' => [
            'required' => 'Staff ID is required',
        ],
        'message' => [
            'required' => 'Message is required',
        ],
    ];

    /**
     * Get SMS logs for a specific staff member
     * 
     * @param int $staffId
     * @param int $limit
     * @return array
     */
    public function getStaffLogs($staffId, $limit = 50)
    {
        return $this->where('staff_id', $staffId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get today's SMS logs
     * 
     * @return array
     */
    public function getTodayLogs()
    {
        $today = date('Y-m-d');
        return $this->where('DATE(created_at)', $today)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get logs by status
     * 
     * @param string $status SENT or FAILED
     * @param int $limit
     * @return array
     */
    public function getLogsByStatus($status, $limit = 100)
    {
        return $this->where('status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get all logs with staff information
     * 
     * @param int $limit
     * @return array
     */
    public function getAllLogsWithStaff($limit = 100)
    {
        return $this->select('staff_sms_logs.*, users.username, users.role')
                    ->join('users', 'users.id = staff_sms_logs.staff_id', 'left')
                    ->orderBy('staff_sms_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get SMS statistics
     * 
     * @return array
     */
    public function getStatistics()
    {
        $db = \Config\Database::connect();
        
        $totalSent = $this->where('status', 'SENT')->countAllResults();
        $totalFailed = $this->where('status', 'FAILED')->countAllResults();
        $todayCount = $this->where('DATE(created_at)', date('Y-m-d'))->countAllResults();
        
        // Get most active staff
        $mostActiveStaff = $db->table('staff_sms_logs')
            ->select('staff_name, COUNT(*) as sms_count')
            ->groupBy('staff_id')
            ->orderBy('sms_count', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return [
            'total_sent' => $totalSent,
            'total_failed' => $totalFailed,
            'today_count' => $todayCount,
            'success_rate' => $totalSent + $totalFailed > 0 
                ? round(($totalSent / ($totalSent + $totalFailed)) * 100, 2) 
                : 0,
            'most_active_staff' => $mostActiveStaff
        ];
    }

    /**
     * Count SMS sent by staff today
     * Used for rate limiting
     * 
     * @param int $staffId
     * @return int
     */
    public function countTodayByStaff($staffId)
    {
        $today = date('Y-m-d');
        return $this->where('staff_id', $staffId)
                    ->where('DATE(created_at)', $today)
                    ->countAllResults();
    }
}
