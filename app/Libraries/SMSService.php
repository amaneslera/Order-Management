<?php

namespace App\Libraries;

/**
 * SMS Service using iProgSMS API
 * Handles all SMS operations for the POS system
 */
class SMSService
{
    private $apiToken;
    private $adminPhone;
    private $apiUrl = 'https://sms.iprogtech.com/api/v1/sms_messages';

    public function __construct()
    {
        // Load SMS configuration from .env using CodeIgniter's env() helper
        helper('filesystem');
        
        $this->apiToken = env('iprogsms.apiToken', '');
        $this->adminPhone = env('sms.adminPhone', '');
        
        // Debug log to verify config is loaded
        log_message('debug', 'SMSService initialized - iProgSMS Token: ' . (empty($this->apiToken) ? 'NOT SET' : 'SET') . ', Admin Phone: ' . $this->adminPhone);
    }

    /**
     * Send SMS to admin
     * 
     * @param string $message The message to send
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function sendToAdmin($message)
    {
        return $this->sendSMS($this->adminPhone, $message);
    }

    /**
     * Send SMS to specific phone number
     * 
     * @param string $phoneNumber Recipient phone number (format: +639XXXXXXXXX or 09XXXXXXXXX)
     * @param string $message The message to send
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function sendSMS($phoneNumber, $message)
    {
        try {
            // Validate configuration
            if (empty($this->apiToken)) {
                return [
                    'success' => false,
                    'message' => 'iProgSMS API Token not configured. Please contact administrator.',
                    'data' => null
                ];
            }

            if (empty($phoneNumber)) {
                return [
                    'success' => false,
                    'message' => 'Admin phone number not configured.',
                    'data' => null
                ];
            }

            // Format phone number (remove + and spaces, keep 639XXXXXXXXX format)
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            // Validate message
            if (empty($message)) {
                return [
                    'success' => false,
                    'message' => 'Message cannot be empty.',
                    'data' => null
                ];
            }

            // Prepare API request data (as per iProgSMS documentation - JSON format)
            $postData = [
                'api_token' => $this->apiToken,
                'phone_number' => $formattedPhone,
                'message' => $message
            ];

            // Send request using cURL with JSON
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Handle cURL errors
            if ($curlError) {
                log_message('error', 'SMS cURL Error: ' . $curlError);
                return [
                    'success' => false,
                    'message' => 'Network error. Please check your internet connection.',
                    'data' => ['error' => $curlError]
                ];
            }

            // Parse response
            $responseData = json_decode($response, true);
            
            // Log the full response for debugging
            log_message('debug', 'SMS API Response (HTTP ' . $httpCode . '): ' . $response);

            // Check if request was successful (status 200 in response body)
            if (isset($responseData['status']) && $responseData['status'] == 200) {
                log_message('info', 'SMS sent successfully to ' . $formattedPhone);
                
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully to admin!',
                    'data' => [
                        'message_id' => $responseData['message_id'] ?? uniqid(),
                        'phone' => $formattedPhone,
                        'status' => 'SENT'
                    ]
                ];
            }

            // Handle API errors
            $errorMessage = 'Failed to send SMS. ';
            if (isset($responseData['message'])) {
                $errorMessage .= $responseData['message'];
            } elseif (isset($responseData['error'])) {
                $errorMessage .= $responseData['error'];
            } else {
                $errorMessage .= 'Please try again later.';
            }

            log_message('error', 'SMS API Error: ' . json_encode($responseData));

            return [
                'success' => false,
                'message' => $errorMessage,
                'data' => $responseData
            ];

        } catch (\Exception $e) {
            log_message('error', 'SMS Exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'An error occurred while sending SMS. Please try again.',
                'data' => ['exception' => $e->getMessage()]
            ];
        }
    }

    /**
     * Format phone number for iProgSMS
     * iProgSMS accepts: 639XXXXXXXXX format
     * 
     * @param string $phoneNumber
     * @return string
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove spaces, dashes, parentheses, and + sign
        $cleaned = preg_replace('/[\s\-\(\)\+]/', '', $phoneNumber);

        // If starts with 0, convert to 63
        if (substr($cleaned, 0, 1) === '0') {
            return '63' . substr($cleaned, 1);
        }

        // If already starts with 63, return as is
        if (substr($cleaned, 0, 2) === '63') {
            return $cleaned;
        }

        // Default: return as is
        return $cleaned;
    }

    /**
     * Get SMS account balance (iProgSMS API)
     * Note: Check iProgSMS documentation for balance endpoint
     * 
     * @return array ['success' => bool, 'balance' => float, 'message' => string]
     */
    public function getBalance()
    {
        // iProgSMS may not have a balance API endpoint in their documentation
        // Return a placeholder response
        return [
            'success' => false,
            'balance' => 0,
            'message' => 'Balance check not available. Please check your iProgSMS dashboard.'
        ];
    }

    /**
     * Validate if SMS service is properly configured
     * 
     * @return array ['configured' => bool, 'errors' => array]
     */
    public function validateConfiguration()
    {
        $errors = [];

        if (empty($this->apiToken)) {
            $errors[] = 'iProgSMS API Token is not configured in .env file';
        }

        if (empty($this->adminPhone)) {
            $errors[] = 'Admin phone number is not configured in .env file';
        }

        return [
            'configured' => empty($errors),
            'errors' => $errors
        ];
    }
}
