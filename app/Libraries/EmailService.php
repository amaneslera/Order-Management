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
			$this->mail->isSMTP();
			$this->mail->Host       = $this->smtpHost;
			$this->mail->SMTPAuth   = true;
			$this->mail->Username   = $this->smtpUser;
			$this->mail->Password   = $this->smtpPassword;
			$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			$this->mail->Port       = $this->smtpPort;
			$this->mail->setFrom($this->fromEmail, $this->fromName);
			$this->mail->CharSet = 'UTF-8';
		} catch (Exception $e) {
			log_message('error', 'Email initialization failed: ' . $e->getMessage());
		}
	}

	/**
	 * Send email
	 */
	public function send($to, $subject, $htmlBody, $plainBody = '', $attachments = [])
	{
		try {
			$this->mail->clearAddresses();
			$this->mail->clearAttachments();
			$this->mail->addAddress($to);
			$this->mail->isHTML(true);
			$this->mail->Subject = $subject;
			$this->mail->Body    = $htmlBody;
			$this->mail->AltBody = $plainBody ?: strip_tags($htmlBody);
			foreach ($attachments as $attachment) {
				if (file_exists($attachment)) {
					$this->mail->addAttachment($attachment);
				}
			}
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
	 */
	public function sendDailySalesReport($recipientEmail, $salesData)
	{
		$subject = 'Daily Sales Report - ' . date('F d, Y');
		$htmlBody = $this->generateSalesReportHTML($salesData);
		$plainBody = $this->generateSalesReportPlain($salesData);
		return $this->send($recipientEmail, $subject, $htmlBody, $plainBody);
	}

	/**
	 * Send Weekly Sales Digest
	 */
	public function sendWeeklyDigest($recipientEmail, $salesData)
	{
		$subject = 'Weekly Sales Digest - ' . ($salesData['period_label'] ?? date('M d, Y'));
		$htmlBody = $this->generateWeeklyDigestHTML($salesData);
		$plainBody = $this->generateWeeklyDigestPlain($salesData);
		return $this->send($recipientEmail, $subject, $htmlBody, $plainBody);
	}

	/**
	 * Generate HTML for daily sales report
	 */
	private function generateSalesReportHTML($salesData)
	{
		// ...existing code for HTML template...
		return '<html><body>Daily Sales Report</body></html>'; // Placeholder
	}

	/**
	 * Generate plain text for daily sales report
	 */
	private function generateSalesReportPlain($salesData)
	{
		// ...existing code for plain text template...
		return 'Daily Sales Report'; // Placeholder
	}

	/**
	 * Generate HTML for weekly digest
	 */
	private function generateWeeklyDigestHTML($salesData)
	{
		// ...existing code for HTML template...
		return '<html><body>Weekly Digest</body></html>'; // Placeholder
	}

	/**
	 * Generate plain text for weekly digest
	 */
	private function generateWeeklyDigestPlain($salesData)
	{
		// ...existing code for plain text template...
		return 'Weekly Digest'; // Placeholder
	}
}
