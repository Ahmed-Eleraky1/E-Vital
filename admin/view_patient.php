<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

// Check if patient_id is provided
if (!isset($_GET['patient_id'])) {
    header("Location: manage_patients.php");
    exit();
}

$patient_id = $_GET['patient_id'];

// Get patient details
$sql_patient = "SELECT * FROM patients WHERE patient_id = ?";
$patient_stmt = $conn->prepare($sql_patient);
$patient_stmt->bind_param("i", $patient_id);
$patient_stmt->execute();
$patient_result = $patient_stmt->get_result();

if ($patient_result->num_rows == 0) {
    header("Location: manage_patients.php");
    exit();
}

$patient = $patient_result->fetch_assoc();

// Get patient's medical records
$sql_records = "SELECT m.*, d.name as doctor_name 
                FROM medicalrecords m 
                LEFT JOIN doctors d ON m.doctor_id = d.doctor_id 
                WHERE m.patient_id = ?";
$records_stmt = $conn->prepare($sql_records);
$records_stmt->bind_param("i", $patient_id);
$records_stmt->execute();
$records_result = $records_stmt->get_result();

// Get patient's prescriptions
$sql_prescriptions = "SELECT p.*, d.name as doctor_name 
                      FROM prescriptions p 
                      LEFT JOIN doctors d ON p.doctor_id = d.doctor_id 
                      WHERE p.patient_id = ?";
$prescriptions_stmt = $conn->prepare($sql_prescriptions);
$prescriptions_stmt->bind_param("i", $patient_id);
$prescriptions_stmt->execute();
$prescriptions_result = $prescriptions_stmt->get_result();

// Get patient's x-rays
$sql_xrays = "SELECT px.*, xt.xray_name, xt.description, xt.radiation_level, xt.required_preparation, d.name as doctor_name 
              FROM patient_xrays px 
              JOIN xraytypes xt ON px.xray_type_id = xt.xray_type_id
              LEFT JOIN doctors d ON px.doctor_id = d.doctor_id
              WHERE px.patient_id = ?";
$xray_stmt = $conn->prepare($sql_xrays);
$xray_stmt->bind_param("i", $patient_id);
$xray_stmt->execute();
$xray_result = $xray_stmt->get_result();

// Get all appointments for the patient
$sql_appointments = "SELECT a.*, d.name as doctor_name 
                     FROM appointments a 
                     LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                     WHERE a.patient_id = ? 
                     ORDER BY a.appointment_date DESC";
$appointments_stmt = $conn->prepare($sql_appointments);
$appointments_stmt->bind_param("i", $patient_id);
$appointments_stmt->execute();
$appointments_result = $appointments_stmt->get_result();

// Get patient's lab tests
$sql_labtests = "SELECT l.*, lt.test_name, lt.description, lt.normal_range, lt.unit, d.name as doctor_name 
                 FROM labtests l 
                 JOIN labtesttypes lt ON l.test_type_id = lt.test_type_id 
                 LEFT JOIN doctors d ON l.doctor_id = d.doctor_id
                 WHERE l.patient_id = ?";
$labtests_stmt = $conn->prepare($sql_labtests);
$labtests_stmt->bind_param("i", $patient_id);
$labtests_stmt->execute();
$labtests_result = $labtests_stmt->get_result();

// Get patient's doctors
$sql_doctors = "SELECT d.* 
                FROM patient_doctors pd 
                JOIN doctors d ON pd.doctor_id = d.doctor_id 
                WHERE pd.patient_id = ?";
$doctors_stmt = $conn->prepare($sql_doctors);
$doctors_stmt->bind_param("i", $patient_id);
$doctors_stmt->execute();
$doctors_result = $doctors_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات المريض - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <style>
        .patient-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .section-header {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .table {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .table thead {
            background-color: #28a745;
            color: white;
        }
        .action-buttons {
            margin-top: 30px;
            margin-bottom: 50px;
        }
        .action-buttons .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>بيانات المريض</h1>
            <a href="manage_patients.php" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة إلى قائمة المرضى
            </a>
        </div>
        
        <div class="patient-info">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-user"></i> المعلومات الشخصية</h5>
                    <hr>
                    <p><strong>الاسم:</strong> <?php echo htmlspecialchars($patient['name']); ?></p>
                    <p><strong>البريد الإلكتروني:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
                    <p><strong>رقم الهاتف:</strong> <?php echo htmlspecialchars($patient['contact_number']); ?></p>
                </div>
                <div class="col-md-6">
                    <h5><i class="fas fa-notes-medical"></i> المعلومات الطبية</h5>
                    <hr>
                    <p><strong>تاريخ الميلاد:</strong> <?php echo htmlspecialchars($patient['date_of_birth']); ?></p>
                    <p><strong>الجنس:</strong> <?php echo htmlspecialchars($patient['gender']); ?></p>
                    <p><strong>فصيلة الدم:</strong> <?php echo htmlspecialchars($patient['blood_type'] ?? 'غير محدد'); ?></p>
                    <p><strong>الحالة:</strong> <span class="badge bg-<?php echo $patient['status'] == 'active' ? 'success' : 'secondary'; ?>">
                        <?php echo $patient['status'] == 'active' ? 'نشط' : 'غير نشط'; ?>
                    </span></p>
                </div>
            </div>
        </div>
        
        <div class="section-header">
            <h3 class="mb-0"><i class="fas fa-user-md"></i> الأطباء المعالجين</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>اسم الطبيب</th>
                        <th>التخصص</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الهاتف</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($doctors_result->num_rows > 0): ?>
                        <?php while($doctor = $doctors_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['specialty']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['contact_number']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">لا يوجد أطباء مسجلين لهذا المريض</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section-header mt-4">
            <h3 class="mb-0"><i class="fas fa-clipboard-list"></i> السجلات الطبية</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>التشخيص</th>
                        <th>العلاج</th>
                        <th>الوصفة</th>
                        <th>الطبيب</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($records_result->num_rows > 0): ?>
                        <?php while($record = $records_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['record_date']); ?></td>
                                <td><?php echo htmlspecialchars($record['diagnosis']); ?></td>
                                <td><?php echo htmlspecialchars($record['treatment']); ?></td>
                                <td><?php echo htmlspecialchars($record['prescription']); ?></td>
                                <td><?php echo htmlspecialchars($record['doctor_name']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">لا توجد سجلات طبية لهذا المريض</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section-header mt-4">
            <h3 class="mb-0"><i class="fas fa-pills"></i> الأدوية الموصوفة</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>اسم الدواء</th>
                        <th>الجرعة</th>
                        <th>التعليمات</th>
                        <th>تاريخ الوصفة</th>
                        <th>الطبيب</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($prescriptions_result->num_rows > 0): ?>
                        <?php while($prescription = $prescriptions_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($prescription['medicine_name']); ?></td>
                                <td><?php echo htmlspecialchars($prescription['dosage']); ?></td>
                                <td><?php echo htmlspecialchars($prescription['instructions']); ?></td>
                                <td><?php echo htmlspecialchars($prescription['prescription_date'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($prescription['doctor_name']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">لا توجد أدوية موصوفة لهذا المريض</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section-header mt-4">
            <h3 class="mb-0"><i class="fas fa-calendar-check"></i> المواعيد</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>تاريخ الموعد</th>
                        <th>الوقت</th>
                        <th>الحالة</th>
                        <th>الملاحظات</th>
                        <th>الطبيب</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($appointments_result->num_rows > 0): ?>
                        <?php while($appointment = $appointments_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $appointment['status'] == 'scheduled' ? 'primary' : 
                                             ($appointment['status'] == 'completed' ? 'success' : 
                                             ($appointment['status'] == 'cancelled' ? 'danger' : 'warning')); 
                                    ?>">
                                        <?php 
                                        $status_text = '';
                                        switch($appointment['status']) {
                                            case 'scheduled': $status_text = 'مجدول'; break;
                                            case 'completed': $status_text = 'مكتمل'; break;
                                            case 'cancelled': $status_text = 'ملغي'; break;
                                            case 'no_show': $status_text = 'لم يحضر'; break;
                                            default: $status_text = $appointment['status'];
                                        }
                                        echo $status_text;
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($appointment['notes']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">لا توجد مواعيد لهذا المريض</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section-header mt-4">
            <h3 class="mb-0"><i class="fas fa-flask"></i> الاختبارات المخبرية</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>اسم الاختبار</th>
                        <th>النتيجة</th>
                        <th>النطاق الطبيعي</th>
                        <th>تاريخ الاختبار</th>
                        <th>الطبيب</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($labtests_result->num_rows > 0): ?>
                        <?php while($labtest = $labtests_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($labtest['test_name']); ?></td>
                                <td><?php echo htmlspecialchars($labtest['result']); ?> <?php echo htmlspecialchars($labtest['unit']); ?></td>
                                <td><?php echo htmlspecialchars($labtest['normal_range']); ?></td>
                                <td><?php echo htmlspecialchars($labtest['test_date']); ?></td>
                                <td><?php echo htmlspecialchars($labtest['doctor_name']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">لا توجد اختبارات مخبرية لهذا المريض</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section-header mt-4">
            <h3 class="mb-0"><i class="fas fa-x-ray"></i> الأشعة</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>نوع الأشعة</th>
                        <th>تاريخ الأشعة</th>
                        <th>الملاحظات</th>
                        <th>الطبيب</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($xray_result->num_rows > 0): ?>
                        <?php while($xray = $xray_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($xray['xray_name']); ?></td>
                                <td><?php echo htmlspecialchars($xray['test_date']); ?></td>
                                <td><?php echo htmlspecialchars($xray['notes']); ?></td>
                                <td><?php echo htmlspecialchars($xray['doctor_name']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">لا توجد أشعة لهذا المريض</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="action-buttons">
            <a href="manage_patients.php" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة إلى قائمة المرضى
            </a>
            <?php if ($patient['status'] == 'active'): ?>
                <a href="update_patient_status.php?patient_id=<?php echo $patient_id; ?>&status=inactive" class="btn btn-danger">
                    <i class="fas fa-user-times"></i> تعطيل حساب المريض
                </a>
            <?php else: ?>
                <a href="update_patient_status.php?patient_id=<?php echo $patient_id; ?>&status=active" class="btn btn-success">
                    <i class="fas fa-user-check"></i> تفعيل حساب المريض
                </a>
            <?php endif; ?>
            <a href="print_patient_report.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-primary" target="_blank">
                <i class="fas fa-print"></i> طباعة تقرير المريض
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>