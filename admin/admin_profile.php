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
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $contact_number = $_POST['contact_number'];
        
        $sql = "UPDATE admins SET name = ?, email = ?, contact_number = ? WHERE admin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $contact_number, $admin_id);
        
        if ($stmt->execute()) {
            $success_message = "تم تحديث البيانات الشخصية بنجاح";
            $_SESSION['email'] = $email; // Update session email
        } else {
            $error_message = "حدث خطأ أثناء تحديث البيانات";
        }
    }
    
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password !== $confirm_password) {
            $password_error = "كلمة المرور الجديدة غير متطابقة";
        } else {
            // Verify current password
            $sql = "SELECT password FROM admins WHERE admin_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
            
            if ($admin['password'] === $current_password) {
                $sql = "UPDATE admins SET password = ? WHERE admin_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $new_password, $admin_id);
                
                if ($stmt->execute()) {
                    $password_success = "تم تغيير كلمة المرور بنجاح";
                } else {
                    $password_error = "حدث خطأ أثناء تغيير كلمة المرور";
                }
            } else {
                $password_error = "كلمة المرور الحالية غير صحيحة";
            }
        }
    }
}

// Get admin details
$sql = "SELECT * FROM admins WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <style>
        .profile-card {
            transition: all 0.3s ease;
        }
        .profile-card:hover {
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
                <!-- Profile Information -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- <h1 class="text-3xl font-bold text-gray-800">إدارة الملف الشخصي</h1> -->
        </div>
                <div class="card profile-card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">البيانات الشخصية</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">الاسم</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="contact_number" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number"
                                       value="<?php echo htmlspecialchars($admin['contact_number'] ?? ''); ?>">
                            </div>

                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التغييرات
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="card profile-card">
                    <div class="card-header">
                        <h4 class="mb-0">تغيير كلمة المرور</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($password_success)): ?>
                            <div class="alert alert-success">
                                <?php echo $password_success; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($password_error)): ?>
                            <div class="alert alert-danger">
                                <?php echo $password_error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">كلمة المرور الحالية</label>
                                <input type="password" class="form-control" id="current_password" 
                                       name="current_password" required>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">كلمة المرور الجديدة</label>
                                <input type="password" class="form-control" id="new_password" 
                                       name="new_password" required>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">تأكيد كلمة المرور الجديدة</label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" required>
                            </div>

                            <button type="submit" name="change_password" class="btn btn-warning">
                                <i class="fas fa-key"></i> تغيير كلمة المرور
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Login History -->
                <div class="card profile-card mt-4">
                    <div class="card-header">
                        <h4 class="mb-0">سجل تسجيل الدخول</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>التاريخ والوقت</th>
                                        <th>عنوان IP</th>
                                        <th>المتصفح</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo date('Y-m-d H:i:s'); ?></td>
                                        <td><?php echo $_SERVER['REMOTE_ADDR']; ?></td>
                                        <td><?php echo $_SERVER['HTTP_USER_AGENT']; ?></td>
                                        <td><span class="badge bg-success">نجاح</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>