<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send SMS to Admin - Coffee Kiosk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary: #6B4423; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, #3E2723 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            border-radius: 10px;
            margin: 5px 10px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .sms-card {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .char-counter {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .char-counter.warning {
            color: #ffc107;
        }
        .char-counter.danger {
            color: #dc3545;
        }
        .log-item {
            border-left: 3px solid;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        .log-item.sent {
            border-color: #28a745;
        }
        .log-item.failed {
            border-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4">
                    <h4><i class="bi bi-shop me-2"></i>POS System</h4>
                    <hr class="border-light">
                </div>
                <nav class="nav flex-column">
                    <a href="<?= base_url('pos') ?>" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a href="<?= base_url('staff/send-sms') ?>" class="nav-link active"><i class="bi bi-chat-text me-2"></i>Message Admin</a>
                    <a href="<?= base_url('pos/search') ?>" class="nav-link"><i class="bi bi-search me-2"></i>Search Order</a>
                    <a href="<?= base_url('pos/orders') ?>" class="nav-link"><i class="bi bi-list-ul me-2"></i>All Orders</a>
                    <?php if (session()->get('role') === 'Admin'): ?>
                        <a href="<?= base_url('admin') ?>" class="nav-link"><i class="bi bi-gear me-2"></i>Admin Panel</a>
                    <?php endif; ?>
                    <hr class="border-light">
                    <a href="<?= base_url('logout') ?>" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </nav>
                <div class="p-4 mt-auto">
                    <small class="text-light">
                        <i class="bi bi-person-circle me-2"></i>
                        <?= esc(session()->get('username')) ?><br>
                        <span class="badge bg-light text-dark mt-1"><?= ucfirst(session()->get('role')) ?></span>
                    </small>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><i class="bi bi-chat-text me-2"></i>Send SMS to Admin</h2>
                        <p class="text-muted">Send urgent messages directly to the administrator</p>
                    </div>
                    <div>
                        <a href="<?= base_url('pos') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>

                <?php if (!$sms_configured): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>SMS Service Not Configured</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($config_errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <small>Please contact the system administrator to configure SMS settings.</small>
                </div>
                <?php endif; ?>

                <div class="row">
                    <!-- SMS Form -->
                    <div class="col-md-6">
                        <div class="card sms-card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-envelope me-2"></i>New Message</h5>
                            </div>
                            <div class="card-body">
                                <form id="smsForm">
                                    <?= csrf_field() ?>
                                    
                                    <div class="mb-3">
                                        <label for="message" class="form-label fw-bold">
                                            Message <span class="text-danger">*</span>
                                        </label>
                                        <textarea 
                                            class="form-control" 
                                            id="message" 
                                            name="message" 
                                            rows="5" 
                                            maxlength="160" 
                                            placeholder="Type your urgent message here..."
                                            <?= !$sms_configured ? 'disabled' : '' ?>
                                            required
                                        ></textarea>
                                        <div class="d-flex justify-content-between mt-2">
                                            <small class="char-counter" id="charCounter">0 / 160 characters</small>
                                            <small class="text-muted">SMS limit: <?= $today_sms_count ?> / <?= $max_sms_per_day ?> today</small>
                                        </div>
                                    </div>

                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Quick Tips:</strong>
                                        <ul class="mb-0 mt-2 small">
                                            <li>Keep messages brief and clear (max 160 chars)</li>
                                            <li>Use for urgent matters only</li>
                                            <li>Admin will receive SMS immediately</li>
                                            <li>Limit: <?= $max_sms_per_day ?> messages per day</li>
                                        </ul>
                                    </div>

                                    <div id="alertContainer"></div>

                                    <button 
                                        type="submit" 
                                        class="btn btn-primary btn-lg w-100" 
                                        id="sendBtn"
                                        <?= !$sms_configured ? 'disabled' : '' ?>
                                    >
                                        <i class="bi bi-send me-2"></i>Send to Admin
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Quick Message Templates -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Templates</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-sm btn-outline-secondary template-btn" data-template="Need supplies: [item name]">
                                        📦 Need Supplies
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary template-btn" data-template="Machine not working: [description]">
                                        ⚙️ Equipment Issue
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary template-btn" data-template="Customer complaint: [details]">
                                        👤 Customer Issue
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary template-btn" data-template="Urgent: [your message]">
                                        🚨 Urgent Message
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SMS History -->
                    <div class="col-md-6">
                        <div class="card sms-card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Messages</h5>
                            </div>
                            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                                <?php if (empty($sms_logs)): ?>
                                    <p class="text-muted text-center py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No messages sent yet
                                    </p>
                                <?php else: ?>
                                    <?php foreach ($sms_logs as $log): ?>
                                    <div class="log-item <?= strtolower($log['status']) ?>">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <?php if ($log['status'] === 'SENT'): ?>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>SENT
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle me-1"></i>FAILED
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted">
                                                <?= date('M d, Y h:i A', strtotime($log['created_at'])) ?>
                                            </small>
                                        </div>
                                        <p class="mb-1"><?= esc($log['message']) ?></p>
                                        <?php if ($log['status'] === 'FAILED' && !empty($log['error_message'])): ?>
                                            <small class="text-danger">
                                                <i class="bi bi-exclamation-circle me-1"></i>
                                                Error: <?= esc($log['error_message']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const messageTextarea = document.getElementById('message');
        const charCounter = document.getElementById('charCounter');
        const sendBtn = document.getElementById('sendBtn');
        const smsForm = document.getElementById('smsForm');
        const alertContainer = document.getElementById('alertContainer');
        const templateButtons = document.querySelectorAll('.template-btn');

        // Character counter
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCounter.textContent = `${length} / 160 characters`;
            
            if (length > 140) {
                charCounter.classList.add('danger');
                charCounter.classList.remove('warning');
            } else if (length > 120) {
                charCounter.classList.add('warning');
                charCounter.classList.remove('danger');
            } else {
                charCounter.classList.remove('warning', 'danger');
            }
        });

        // Template buttons
        templateButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                messageTextarea.value = this.dataset.template;
                messageTextarea.focus();
                messageTextarea.dispatchEvent(new Event('input'));
            });
        });

        // Form submission
        smsForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const message = messageTextarea.value.trim();
            
            if (!message) {
                showAlert('danger', 'Please enter a message');
                return;
            }

            if (message.length > 160) {
                showAlert('danger', 'Message too long. Maximum 160 characters.');
                return;
            }

            // Disable button and show loading
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

            try {
                const formData = new FormData(smsForm);
                
                const response = await fetch('<?= base_url('staff/send-sms') ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('success', result.message);
                    messageTextarea.value = '';
                    charCounter.textContent = '0 / 160 characters';
                    
                    // Reload page after 2 seconds to show new message in history
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert('danger', result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Network error. Please check your connection and try again.');
            } finally {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="bi bi-send me-2"></i>Send to Admin';
            }
        });

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            alertContainer.innerHTML = alertHtml;

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }
    </script>
</body>
</html>
