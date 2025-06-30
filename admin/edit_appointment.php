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

if (!isset($_GET['id'])) {
    header("Location: manage_appointments.php");
    exit();
}

$appointment_id = $_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_date = $_POST['appointment_date'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];

    $allowed_statuses = ['pending', 'approved', 'Completed', 'Cancelled'];
if (!in_array($status, $allowed_statuses)) {
    $error_message = "قيمة الحالة غير صالحة.";
}
    
    $sql = "UPDATE appointments 
            SET appointment_date = ?, status = ?, notes = ?, created_at = CURRENT_TIMESTAMP 
            WHERE appointment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $appointment_date, $status, $notes, $appointment_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "تم تحديث الموعد بنجاح";
        header("Location: manage_appointments.php");
        exit();
    } else {
        $error_message = "حدث خطأ أثناء تحديث الموعد";
    }
}

// Get appointment details
$sql = "SELECT 
    a.*,
    p.name as patient_name,
    p.contact_number as patient_contact_number,
   p.medical_condition,
    d.name as doctor_name,
    d.specialty as doctor_specialty,
    d.working_hours
FROM appointments a
JOIN patients p ON a.patient_id = p.patient_id
JOIN doctors d ON a.doctor_id = d.doctor_id
WHERE a.appointment_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();

if (!$appointment) {
    header("Location: manage_appointments.php");
    exit();
}

// Get doctor's working hours
$working_hours = [];
if (!empty($appointment['working_hours'])) {
    $decoded = json_decode($appointment['working_hours'], true);
    if (is_array($decoded)) {
        $working_hours = $decoded;
    }
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل موعد - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <style>
        .info-card {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .info-card i {
            color: #28a745;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
                            <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
    
    <div class="container mt-4 mr-64" >
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">تعديل موعد</h4>
                        
                    </div>
                    <div class="card-body">


                        <!-- Patient Information -->
                        <div class="info-card">
                            <h5>معلومات المريض</h5>
                            <p class="mb-2">
                                <i class="fas fa-user"></i>
                                <strong>الاسم:</strong> <?php echo htmlspecialchars($appointment['patient_name']); ?>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-contact_number"></i>
                                <strong>رقم الهاتف:</strong> <?php echo htmlspecialchars($appointment['patient_contact_number']); ?>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-notes-medical"></i>
                                <strong>الحالة المرضية:</strong> <?php echo ($appointment['medical_condition']); ?>
                            </p>
                        </div>

                        <!-- Doctor Information -->
                        <div class="info-card">
                            <h5>معلومات الطبيب</h5>
                            <p class="mb-2">
                                <i class="fas fa-user-md"></i>
                                <strong>الاسم:</strong> د. <?php echo htmlspecialchars($appointment['doctor_name']); ?>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-stethoscope"></i>
                                <strong>التخصص:</strong> <?php echo htmlspecialchars($appointment['doctor_specialty']); ?>
                            </p>
                        </div>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="appointment_date" class="form-label">تاريخ ووقت الموعد</label>
                                <input type="datetime-local" class="form-control" id="appointment_date" 
                                       name="appointment_date" 
                                       value="<?php echo date('Y-m-d\TH:i', strtotime($appointment['appointment_date'])); ?>" 
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">حالة الموعد</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" <?php echo $appointment['status'] == 'pending' ? 'selected' : ''; ?>>pending</option>
                                    <option value="Scheduled" <?php echo $appointment['status'] == 'Scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                    <option value="Completed" <?php echo $appointment['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Cancelled" <?php echo $appointment['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($appointment['notes'] ?? ''); ?></textarea>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ التغييرات
                                </button>
                                <a href="manage_appointments.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>