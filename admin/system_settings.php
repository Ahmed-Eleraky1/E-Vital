<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';
$admin_id = $_SESSION['admin_id'];
$sql_admin = "SELECT name FROM admins WHERE admin_id = '$admin_id'";
$admin_result = $conn->query($sql_admin);
$admin_name = $admin_result && $admin_result->num_rows > 0 ? $admin_result->fetch_assoc()['name'] : 'مدير';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // System Settings
        $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
        $appointment_buffer = $_POST['appointment_buffer'];
        $max_appointments_per_day = $_POST['max_appointments_per_day'];
        $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
        $sms_notifications = isset($_POST['sms_notifications']) ? 1 : 0;
        
        // Email Settings
        $smtp_host = $_POST['smtp_host'];
        $smtp_port = $_POST['smtp_port'];
        $smtp_username = $_POST['smtp_username'];
        $smtp_password = $_POST['smtp_password'];
        $smtp_encryption = $_POST['smtp_encryption'];
        $from_email = $_POST['from_email'];
        $from_name = $_POST['from_name'];
        
        // SMS Settings
        $sms_provider = $_POST['sms_provider'];
        $sms_api_key = $_POST['sms_api_key'];
        $sms_sender_id = $_POST['sms_sender_id'];
        
        // Update settings
        $settings = [
            'maintenance_mode' => $maintenance_mode,
            'appointment_buffer' => $appointment_buffer,
            'max_appointments_per_day' => $max_appointments_per_day,
            'email_notifications' => $email_notifications,
            'sms_notifications' => $sms_notifications,
            'smtp_host' => $smtp_host,
            'smtp_port' => $smtp_port,
            'smtp_username' => $smtp_username,
            'smtp_password' => $smtp_password,
            'smtp_encryption' => $smtp_encryption,
            'from_email' => $from_email,
            'from_name' => $from_name,
            'sms_provider' => $sms_provider,
            'sms_api_key' => $sms_api_key,
            'sms_sender_id' => $sms_sender_id
        ];
        
        foreach ($settings as $key => $value) {
            $sql = "INSERT INTO system_settings (setting_key, value) 
                   VALUES (?, ?) 
                   ON DUPLICATE KEY UPDATE value = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $key, $value, $value);
            $stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        $_SESSION['success_message'] = "تم تحديث إعدادات النظام بنجاح";
        header("Location: system_settings.php");
        exit();
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $error_message = "حدث خطأ أثناء تحديث الإعدادات: " . $e->getMessage();
    }
}

// Get current settings
$settings = [];
$sql = "SELECT setting_key, value FROM system_settings";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['value'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعدادات النظام - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <style>
        .settings-card {
            transition: all 0.3s ease;
        }
        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="mr-64 p-6">
        <div class="row justify-content-center">
            <div class="col-md-10" >
                <form method="POST" action="">
                    <!-- System Settings -->
                    <div class="card settings-card mb-4 mt-4">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-cogs"></i> إعدادات النظام الأساسية
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" class="form-check-input" id="maintenance_mode" 
                                               name="maintenance_mode" 
                                               <?php echo ($settings['maintenance_mode'] ?? 0) == 1 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="maintenance_mode">
                                            وضع الصيانة
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="appointment_buffer" class="form-label">
                                            المدة بين المواعيد (بالدقائق)
                                        </label>
                                        <input type="number" class="form-control" id="appointment_buffer" 
                                               name="appointment_buffer" 
                                               value="<?php echo $settings['appointment_buffer'] ?? 30; ?>" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="max_appointments_per_day" class="form-label">
                                            الحد الأقصى للمواعيد في اليوم
                                        </label>
                                        <input type="number" class="form-control" id="max_appointments_per_day" 
                                               name="max_appointments_per_day" 
                                               value="<?php echo $settings['max_appointments_per_day'] ?? 20; ?>" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" class="form-check-input" id="email_notifications" 
                                               name="email_notifications" 
                                               <?php echo ($settings['email_notifications'] ?? 0) == 1 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="email_notifications">
                                            تفعيل إشعارات البريد الإلكتروني
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="sms_notifications" 
                                               name="sms_notifications" 
                                               <?php echo ($settings['sms_notifications'] ?? 0) == 1 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="sms_notifications">
                                            تفعيل إشعارات الرسائل النصية
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Settings -->
                    <div class="card settings-card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-envelope"></i> إعدادات البريد الإلكتروني
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_host" class="form-label">SMTP Host</label>
                                        <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                               value="<?php echo $settings['smtp_host'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_port" class="form-label">SMTP Port</label>
                                        <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                               value="<?php echo $settings['smtp_port'] ?? 587; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_username" class="form-label">SMTP Username</label>
                                        <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                                               value="<?php echo $settings['smtp_username'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_password" class="form-label">SMTP Password</label>
                                        <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                               value="<?php echo $settings['smtp_password'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="smtp_encryption" class="form-label">SMTP Encryption</label>
                                        <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                            <option value="tls" <?php echo ($settings['smtp_encryption'] ?? '') == 'tls' ? 'selected' : ''; ?>>TLS</option>
                                            <option value="ssl" <?php echo ($settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="from_email" class="form-label">From Email</label>
                                        <input type="email" class="form-control" id="from_email" name="from_email" 
                                               value="<?php echo $settings['from_email'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="from_name" class="form-label">From Name</label>
                                        <input type="text" class="form-control" id="from_name" name="from_name" 
                                               value="<?php echo $settings['from_name'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SMS Settings -->
                    <div class="card settings-card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-sms"></i> إعدادات الرسائل النصية
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="sms_provider" class="form-label">مزود خدمة الرسائل</label>
                                        <select class="form-select" id="sms_provider" name="sms_provider">
                                            <option value="">اختر مزود الخدمة</option>
                                            <option value="twilio" <?php echo ($settings['sms_provider'] ?? '') == 'twilio' ? 'selected' : ''; ?>>Twilio</option>
                                            <option value="nexmo" <?php echo ($settings['sms_provider'] ?? '') == 'nexmo' ? 'selected' : ''; ?>>Nexmo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="sms_api_key" class="form-label">API Key</label>
                                        <input type="text" class="form-control" id="sms_api_key" name="sms_api_key" 
                                               value="<?php echo $settings['sms_api_key'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="sms_sender_id" class="form-label">Sender ID</label>
                                        <input type="text" class="form-control" id="sms_sender_id" name="sms_sender_id" 
                                               value="<?php echo $settings['sms_sender_id'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> حفظ الإعدادات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Toggle email settings based on email notifications checkbox
    document.getElementById('email_notifications').addEventListener('change', function() {
        const emailSettings = document.querySelectorAll('#smtp_host, #smtp_port, #smtp_username, #smtp_password, #smtp_encryption, #from_email, #from_name');
        emailSettings.forEach(el => el.disabled = !this.checked);
    });

    // Toggle SMS settings based on SMS notifications checkbox
    document.getElementById('sms_notifications').addEventListener('change', function() {
        const smsSettings = document.querySelectorAll('#sms_provider, #sms_api_key, #sms_sender_id');
        smsSettings.forEach(el => el.disabled = !this.checked);
    });

    // Initialize settings state
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('email_notifications').dispatchEvent(new Event('change'));
        document.getElementById('sms_notifications').dispatchEvent(new Event('change'));
    });
    </script>
</body>
</html>