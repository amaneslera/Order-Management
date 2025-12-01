<?php

namespace App\Controllers;

use App\Libraries\SMSService;
use App\Models\SMSLogModel;
use App\Models\ActivityLogModel;

class StaffMessagingController extends BaseController
{
    protected $smsService;
    protected $smsLogModel;
    protected $activityLog;

    // Rate limiting: Maximum SMS per staff per day
    private $maxSmsPerDay = 10;

    public function __construct()
    {
        $this->smsService = new SMSService();
        $this->smsLogModel = new SMSLogModel();
        $this->activityLog = new ActivityLogModel();
    }

    /**
     * Check if user is logged in as staff (cashier or admin)
     */
    private function checkAuth()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to access this page');
        }
        return null;
    }

    /**
     * Show SMS form page
     */
    public function index()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        // Get staff's SMS history
        $staffId = session()->get('user_id');
        $data['sms_logs'] = $this->smsLogModel->getStaffLogs($staffId, 20);
        
        // Get today's SMS count for rate limiting
        $data['today_sms_count'] = $this->smsLogModel->countTodayByStaff($staffId);
        $data['max_sms_per_day'] = $this->maxSmsPerDay;
        
        // Check SMS service configuration
        $configCheck = $this->smsService->validateConfiguration();
        $data['sms_configured'] = $configCheck['configured'];
        $data['config_errors'] = $configCheck['errors'];

        return view('staff/send_sms', $data);
    }

    /**
     * Send SMS to admin (POST)
     */
    public function sendToAdmin()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        // Validate CSRF token
        if (!$this->validate(['csrf_test_name' => 'required'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request. Please refresh the page and try again.'
            ]);
        }

        $staffId = session()->get('user_id');
        $staffName = session()->get('username');
        $message = $this->request->getPost('message');

        // Validate message
        if (empty($message)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Message cannot be empty.'
            ]);
        }

        // Trim and validate length
        $message = trim($message);
        if (strlen($message) > 160) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Message too long. Maximum 160 characters allowed.'
            ]);
        }

        if (strlen($message) < 5) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Message too short. Please provide more details.'
            ]);
        }

        // Rate limiting check
        $todayCount = $this->smsLogModel->countTodayByStaff($staffId);
        if ($todayCount >= $this->maxSmsPerDay) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Daily SMS limit reached ({$this->maxSmsPerDay} messages per day). Please contact admin directly."
            ]);
        }

        // Get admin phone from .env
        $adminPhone = getenv('sms.adminPhone') ?: '';

        // Prepare log data
        $logData = [
            'staff_id' => $staffId,
            'staff_name' => $staffName,
            'message' => $message,
            'admin_phone' => $adminPhone,
            'status' => 'FAILED',
            'error_message' => null,
            'sms_id' => null,
            'sent_at' => null,
        ];

        try {
            // Send SMS
            $result = $this->smsService->sendToAdmin($message);

            if ($result['success']) {
                // Update log data with success
                $logData['status'] = 'SENT';
                $logData['sent_at'] = date('Y-m-d H:i:s');
                $logData['sms_id'] = $result['data']['message_id'] ?? null;

                // Save log
                $this->smsLogModel->insert($logData);

                // Log activity
                $this->activityLog->logActivity(
                    $staffId,
                    'sms_sent',
                    "Sent SMS to admin: " . substr($message, 0, 50) . (strlen($message) > 50 ? '...' : '')
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => '✅ SMS sent successfully to admin!',
                    'remaining' => $this->maxSmsPerDay - ($todayCount + 1)
                ]);
            } else {
                // Update log data with error
                $logData['error_message'] = $result['message'];
                $this->smsLogModel->insert($logData);

                return $this->response->setJSON([
                    'success' => false,
                    'message' => '❌ ' . $result['message']
                ]);
            }

        } catch (\Exception $e) {
            // Log error
            $logData['error_message'] = $e->getMessage();
            $this->smsLogModel->insert($logData);

            log_message('error', 'SMS sending exception: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => '❌ An error occurred while sending SMS. Please try again later.'
            ]);
        }
    }

    /**
     * View SMS logs for current staff
     */
    public function logs()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $staffId = session()->get('user_id');
        $data['sms_logs'] = $this->smsLogModel->getStaffLogs($staffId, 100);
        $data['today_count'] = $this->smsLogModel->countTodayByStaff($staffId);

        return view('staff/sms_logs', $data);
    }

    /**
     * Check SMS balance (AJAX)
     */
    public function checkBalance()
    {
        $authCheck = $this->checkAuth();
        if ($authCheck) return $authCheck;

        $balance = $this->smsService->getBalance();
        return $this->response->setJSON($balance);
    }
}
