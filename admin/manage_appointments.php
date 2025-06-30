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

// Handle appointment status updates
if (isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['status'];
    
    $sql = "UPDATE appointments SET status = ? WHERE appointment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $appointment_id);
    $stmt->execute();
}

// Filter appointments
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

$sql = "SELECT 
    a.*,
    p.name as patient_name,
    p.contact_number as patient_contact_number,
    d.name as doctor_name,
    d.specialty as doctor_specialty
FROM appointments a
JOIN patients p ON a.patient_id = p.patient_id
JOIN doctors d ON a.doctor_id = d.doctor_id
WHERE 1=1";

if ($status_filter) {
    $sql .= " AND a.status = '$status_filter'";
}
if ($date_filter) {
    $sql .= " AND DATE(a.appointment_date) = '$date_filter'";
}

$sql .= " ORDER BY a.appointment_date DESC";
$result = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المواعيد - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
        }
        .card-body {
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .appointment-card {
            transition: transform 0.3s;
            border-radius: 15px;
            height: 100%;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            border: none;
            overflow: hidden;
        }
        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .status-badge {
            font-size: 0.85em;
            padding: 5px 12px;
            border-radius: 10px;
            font-weight: 600;
        }
        .card-header {
            background-color: #f1f8ff !important;
            border-bottom: 1px solid #e0e0e0;
            padding: 15px 20px;
        }
        .card-footer {
            background-color: #f1f8ff !important;
            border-top: 1px solid #e0e0e0;
            padding: 15px 20px;
        }
        .appointments-container .row {
            display: flex;
            flex-wrap: wrap;
        }
        .appointments-container .col-md-6 {
            /* display: flex; */
            margin-bottom: 25px;
        }
        
        /* التنسيق الداخلي الجديد */
        .card-body-content {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 0 10px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            align-items: center;
        }
        .info-label {
            font-weight: 700;
            color: #495057;
            font-size: 0.9rem;
            min-width: 100px;
        }
        .info-value {
            color: #212529;
            text-align: left;
            font-size: 0.95rem;
            word-break: break-word;
            max-width: 60%;
        }
        .notes-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: auto;
        }
        .notes-label {
            font-weight: 700;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }
        
        /* تحسينات الأزرار */
        .form-select-sm-custom {
            height: 38px;
            padding: 4px 50px 4px 12px;
            text-align: right;
            border-radius: 8px;
            border: 1px solid #ced4da;
        }
        .btn-sm-custom {
            height: 38px;
            padding: 5px 2.5rem;
            border-radius: 8px;
            font-weight: 500;
            margin-right: 5.2rem;
        }
        .btn-delete-custom {
            height: 38px;
            padding: 4px 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .appointment-card {
                min-height: auto;
            }
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }
            .info-label {
                margin-bottom: 5px;
            }
            .info-value {
                max-width: 100%;
            }
            .form-select-sm-custom,
            .btn-sm-custom,
            .btn-delete-custom {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container-fluid p-4 p-md-5" style="margin-right: 15rem;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 font-weight-bold text-gray-800">إدارة المواعيد</h1>
        </div>
        
        <!-- فلترة المواعيد -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body py-3">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label mb-1">حالة الموعد</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>معلق</option>
                            <option value="Scheduled" <?= $status_filter == 'Scheduled' ? 'selected' : '' ?>>مؤكد</option>
                            <option value="completed" <?= $status_filter == 'completed' ? 'selected' : '' ?>>مكتمل</option>
                            <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>ملغي</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">التاريخ</label>
                        <input type="date" name="date" class="form-control" value="<?= $date_filter ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-filter"></i> تصفية المواعيد
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- قائمة المواعيد -->
        <div class="row appointments-container">
            <?php if ($result->num_rows > 0): ?>
                <?php while($appointment = $result->fetch_assoc()): ?>
                    <div class="col-md-6">
                        <div class="card appointment-card border-0 shadow-sm h-100">
                            <div class="card-header border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 font-weight-bold">موعد #<?= $appointment['appointment_id'] ?></h5>
                                    <span class="badge status-badge bg-<?= 
                                        $appointment['status'] == 'pending' ? 'warning' : 
                                        ($appointment['status'] == 'Scheduled' ? 'success' : 
                                        ($appointment['status'] == 'completed' ? 'primary' : 
                                        ($appointment['status'] == 'cancelled' ? 'danger' : 'secondary')))
                                    ?>">
                                        <?= [
                                            'pending' => 'معلق',
                                            'Scheduled' => 'مؤكد',
                                            'completed' => 'مكتمل',
                                            'cancelled' => 'ملغي'
                                        ][$appointment['status']] ?? $appointment['status'] ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="card-body-content">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h6 class="text-center mb-3 text-primary">معلومات المريض</h6>
                                                <div class="info-row">
                                                    <span class="info-label">الاسم:</span>
                                                    <span class="info-value"><?= htmlspecialchars($appointment['patient_name']) ?></span>
                                                </div>
                                                <div class="info-row">
                                                    <span class="info-label">رقم الهاتف:</span>
                                                    <span class="info-value"><?= htmlspecialchars($appointment['patient_contact_number']) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h6 class="text-center mb-3 text-primary">معلومات الطبيب</h6>
                                                <div class="info-row">
                                                    <span class="info-label">الاسم:</span>
                                                    <span class="info-value"><?= htmlspecialchars($appointment['doctor_name']) ?></span>
                                                </div>
                                                <div class="info-row">
                                                    <span class="info-label">التخصص:</span>
                                                    <span class="info-value"><?= htmlspecialchars($appointment['doctor_specialty']) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="info-box mt-2">
                                        <div class="info-row">
                                            <span class="info-label">تاريخ الموعد:</span>
                                            <span class="info-value"><?= date('Y-m-d H:i', strtotime($appointment['appointment_date'])) ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="notes-box mt-auto">
                                        <span class="notes-label">ملاحظات:</span>
                                        <p class="mb-0"><?= !empty($appointment['notes']) ? nl2br(htmlspecialchars($appointment['notes'])) : 'لا توجد ملاحظات' ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer border-0">
                                <div class="d-flex flex-wrap justify-content-between">
                                    <form method="POST" class="d-flex">
                                        <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
                                        <select name="status" class="form-select form-select-sm form-select-sm-custom">
                                            <option value="pending" <?= $appointment['status'] == 'pending' ? 'selected' : '' ?>>معلق</option>
                                            <option value="Scheduled" <?= $appointment['status'] == 'Scheduled' ? 'selected' : '' ?>>مؤكد</option>
                                            <option value="completed" <?= $appointment['status'] == 'completed' ? 'selected' : '' ?>>مكتمل</option>
                                            <option value="cancelled" <?= $appointment['status'] == 'cancelled' ? 'selected' : '' ?>>ملغي</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-primary btn-sm btn-sm-custom">
                                            <i class="fas fa-save"><span style=" padding-right: 4px;">تحديث</span></i>
                                        </button> 
                                        
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete-custom" 
                                            onclick="deleteAppointment(<?= $appointment['appointment_id'] ?>)" style="margin-left: 1rem; padding: 0 2.5rem;">
                                        <i class="fas fa-trash"></i> حذف
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-calendar-times fa-2x mb-3"></i>
                        <h4 class="alert-heading">لا توجد مواعيد</h4>
                        <p>لم يتم العثور على مواعيد تطابق معايير البحث المحددة</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function deleteAppointment(appointmentId) {
        if (confirm('هل أنت متأكد من رغبتك في حذف هذا الموعد؟ لا يمكن التراجع عن هذه العملية.')) {
            window.location.href = `delete_appointment.php?id=${appointmentId}`;
        }
    }
    </script>
</body>
</html>