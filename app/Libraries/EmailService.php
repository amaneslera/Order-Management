<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Email Service using PHPMailer
 * Handles all email operations for the POS system
 */
class EmailService
{
    private $mail;
    private $fromEmail;
    private $fromName;
    private $smtpHost;
    private $smtpPort;
    private $smtpUser;
    private $smtpPassword;

    public function __construct()
    {
        // Load email configuration from .env
        $this->fromEmail = getenv('email.fromEmail') ?: 'your-email@gmail.com';
        $this->fromName = getenv('email.fromName') ?: 'Coffee Kiosk POS';
        $this->smtpHost = getenv('email.SMTPHost') ?: 'smtp.gmail.com';
        $this->smtpPort = getenv('email.SMTPPort') ?: 587;
        $this->smtpUser = getenv('email.SMTPUser') ?: 'your-email@gmail.com';
        $this->smtpPassword = getenv('email.SMTPPass') ?: 'your-app-password';

        $this->initializeMailer();
    }

    /**
     * Initialize PHPMailer with SMTP settings
     */
    private function initializeMailer()
    {
        $this->mail = new PHPMailer(true);
        
        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host       = $this->smtpHost;
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $this->smtpUser;
            $this->mail->Password   = $this->smtpPassword;
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = $this->smtpPort;
            
            // Sender info
            $this->mail->setFrom($this->fromEmail, $this->fromName);
            
            // Character set
            $this->mail->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            log_message('error', 'Email initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Send email
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $htmlBody HTML email body
     * @param string $plainBody Plain text email body (optional)
     * @param array $attachments Array of file paths to attach (optional)
     * @return array ['success' => bool, 'message' => string]
     */
    public function send($to, $subject, $htmlBody, $plainBody = '', $attachments = [])
    {
        try {
            // Reset recipients for new email
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            // Recipient
            $this->mail->addAddress($to);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $htmlBody;
            $this->mail->AltBody = $plainBody ?: strip_tags($htmlBody);
            
            // Add attachments if any
            foreach ($attachments as $attachment) {
                if (file_exists($attachment)) {
                    $this->mail->addAttachment($attachment);
                }
            }
            
            // Send email
            $this->mail->send();
            
            return [
                'success' => true,
                'message' => 'Email sent successfully to ' . $to
            ];
            
        } catch (Exception $e) {
            log_message('error', 'Email sending failed: ' . $this->mail->ErrorInfo);
            
            return [
                'success' => false,
                'message' => 'Email could not be sent. Error: ' . $this->mail->ErrorInfo
            ];
        }
    }

    /**
     * Send Daily Sales Report
     * 
     * @param string $recipientEmail
     * @param array $salesData
     * @return array
     */
    public function sendDailySalesReport($recipientEmail, $salesData)
    {
        $subject = 'Daily Sales Report - ' . date('F d, Y');
        
        $htmlBody = $this->generateSalesReportHTML($salesData);
        $plainBody = $this->generateSalesReportPlain($salesData);
        
        return $this->send($recipientEmail, $subject, $htmlBody, $plainBody);
    }

    /**
     * Generate HTML for sales report
     * 
     * @param array $salesData
     * @return string
     */
    private function generateSalesReportHTML($salesData)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #6B4423 0%, #3E2723 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 10px 10px; }
                .stats { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
                .stat-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
                .stat-label { font-weight: bold; color: #6B4423; }
                .stat-value { color: #333; font-size: 1.1em; }
                .highlight { background: #6B4423; color: white; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0; }
                .highlight h2 { margin: 0; font-size: 2em; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; background: white; }
                th { background: #6B4423; color: white; padding: 10px; text-align: left; }
                td { padding: 10px; border-bottom: 1px solid #eee; }
                .footer { text-align: center; padding: 20px; color: #999; font-size: 0.9em; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>☕ Daily Sales Report</h1>
                    <p>' . date('l, F d, Y') . '</p>
                </div>
                <div class="content">
                    <div class="highlight">
                        <p style="margin: 0; opacity: 0.8;">Total Revenue</p>
                        <h2>₱' . number_format($salesData['total_revenue'], 2) . '</h2>
                    </div>
                    
                    <div class="stats">
                        <h3>📊 Summary</h3>
                        <div class="stat-row">
                            <span class="stat-label">Total Orders:</span>
                            <span class="stat-value">' . $salesData['total_orders'] . '</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Completed Orders:</span>
                            <span class="stat-value">' . $salesData['completed_orders'] . '</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Pending Orders:</span>
                            <span class="stat-value">' . $salesData['pending_orders'] . '</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Average Order Value:</span>
                            <span class="stat-value">₱' . number_format($salesData['average_order_value'], 2) . '</span>
                        </div>
                    </div>';

        // Top Selling Items
        if (!empty($salesData['top_items'])) {
            $html .= '
                    <div class="stats">
                        <h3>🏆 Top Selling Items</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>';
            
            foreach ($salesData['top_items'] as $item) {
                $html .= '
                                <tr>
                                    <td>' . htmlspecialchars($item['name']) . '</td>
                                    <td>' . $item['quantity'] . '</td>
                                    <td>₱' . number_format($item['revenue'], 2) . '</td>
                                </tr>';
            }
            
            $html .= '
                            </tbody>
                        </table>
                    </div>';
        }

        // Payment Methods
        if (!empty($salesData['payment_methods'])) {
            $html .= '
                    <div class="stats">
                        <h3>💳 Payment Methods</h3>';
            
            foreach ($salesData['payment_methods'] as $method => $amount) {
                $html .= '
                        <div class="stat-row">
                            <span class="stat-label">' . ucfirst($method) . ':</span>
                            <span class="stat-value">₱' . number_format($amount, 2) . '</span>
                        </div>';
            }
            
            $html .= '
                    </div>';
        }

        $html .= '
                </div>
                <div class="footer">
                    <p>This is an automated report from Coffee Kiosk POS System</p>
                    <p>Generated on ' . date('Y-m-d H:i:s') . '</p>
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Generate plain text for sales report
     * 
     * @param array $salesData
     * @return string
     */
    private function generateSalesReportPlain($salesData)
    {
        $text = "DAILY SALES REPORT\n";
        $text .= date('l, F d, Y') . "\n";
        $text .= str_repeat('=', 50) . "\n\n";
        
        $text .= "TOTAL REVENUE: ₱" . number_format($salesData['total_revenue'], 2) . "\n\n";
        
        $text .= "SUMMARY\n";
        $text .= "-------\n";
        $text .= "Total Orders: " . $salesData['total_orders'] . "\n";
        $text .= "Completed Orders: " . $salesData['completed_orders'] . "\n";
        $text .= "Pending Orders: " . $salesData['pending_orders'] . "\n";
        $text .= "Average Order Value: ₱" . number_format($salesData['average_order_value'], 2) . "\n\n";
        
        if (!empty($salesData['top_items'])) {
            $text .= "TOP SELLING ITEMS\n";
            $text .= "-----------------\n";
            foreach ($salesData['top_items'] as $item) {
                $text .= $item['name'] . " - Qty: " . $item['quantity'] . " - ₱" . number_format($item['revenue'], 2) . "\n";
            }
            $text .= "\n";
        }
        
        if (!empty($salesData['payment_methods'])) {
            $text .= "PAYMENT METHODS\n";
            $text .= "---------------\n";
            foreach ($salesData['payment_methods'] as $method => $amount) {
                $text .= ucfirst($method) . ": ₱" . number_format($amount, 2) . "\n";
            }
        }
        
        $text .= "\n" . str_repeat('=', 50) . "\n";
        $text .= "Generated on " . date('Y-m-d H:i:s') . "\n";
        
        return $text;
    }
}
