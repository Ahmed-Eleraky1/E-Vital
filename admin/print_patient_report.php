<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

// Check if patient_id is provided
if (!isset($_GET['patient_id'])) {
    die("خطأ: لم يتم تحديد معرف المريض");
}

$patient_id = $_GET['patient_id'];

// Get patient details
$sql_patient = "SELECT * FROM patients WHERE patient_id = ?";
$patient_stmt = $conn->prepare($sql_patient);
$patient_stmt->bind_param("i", $patient_id);
$patient_stmt->execute();
$patient_result = $patient_stmt->get_result();

if ($patient_result->num_rows == 0) {
    die("خطأ: لم يتم العثور على المريض");
}

$patient = $patient_result->fetch_assoc();

// Get patient's medical records
$sql_records = "SELECT m.*, d.name as doctor_name 
                FROM medicalrecords m 
                LEFT JOIN doctors d ON m.doctor_id = d.doctor_id 
                WHERE m.patient_id = ?
                ORDER BY m.created_at DESC";
$records_stmt = $conn->prepare($sql_records);
$records_stmt->bind_param("i", $patient_id);
$records_stmt->execute();
$records_result = $records_stmt->get_result();

// Get patient's prescriptions
$sql_prescriptions = "SELECT p.*, d.name as doctor_name 
                      FROM prescriptions p 
                      LEFT JOIN doctors d ON p.doctor_id = d.doctor_id 
                      WHERE p.patient_id = ?
                      ORDER BY p.prescribed_date DESC";
$prescriptions_stmt = $conn->prepare($sql_prescriptions);
$prescriptions_stmt->bind_param("i", $patient_id);
$prescriptions_stmt->execute();
$prescriptions_result = $prescriptions_stmt->get_result();

// Get patient's lab tests
$sql_labtests = "SELECT l.*, lt.test_name, lt.description, lt.normal_range, lt.unit, d.name as doctor_name 
                 FROM labtests l 
                 JOIN labtesttypes lt ON l.test_type_id = lt.test_type_id 
                 LEFT JOIN doctors d ON l.doctor_id = d.doctor_id
                 WHERE l.patient_id = ?
                 ORDER BY l.test_date DESC";
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

// Get current date and time
$report_date = date("Y-m-d H:i:s");

// Get admin info
$admin_id = $_SESSION['admin_id'];
$sql_admin = "SELECT name FROM admins WHERE admin_id = ?";
$admin_stmt = $conn->prepare($sql_admin);
$admin_stmt->bind_param("i", $admin_id);
$admin_stmt->execute();
$admin_result = $admin_stmt->get_result();
$admin = $admin_result->fetch_assoc();
$admin_name = $admin ? $admin['name'] : 'مدير النظام';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير المريض: <?php echo htmlspecialchars($patient['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                font-size: 12pt;
                color: #000;
                background-color: #fff;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
            .print-container {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 0;
            }
            .table {
                border-collapse: collapse !important;
            }
            .table td, .table th {
                background-color: #fff !important;
                border: 1px solid #000 !important;
            }
            .header-info {
                border-bottom: 1px solid #000;
                margin-bottom: 20px;
                padding-bottom: 10px;
            }
        }
        
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .print-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .report-title {
            text-align: center;
            margin-bottom: 30px;
            color: #28a745;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }
        .patient-info {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .section-title {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 30px 0 15px 0;
        }
        .table {
            margin-bottom: 30px;
        }
        .report-footer {
            margin-top: 50px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            font-size: 0.9em;
            color: #6c757d;
        }
        .btn-print {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 999;
        }
    </style>
</head>
<body>
    <button class="btn btn-primary btn-print no-print" onclick="window.print()">
        <i class="fas fa-print"></i> طباعة التقرير
    </button>
    
    <div class="container print-container">
        <div class="report-title">
            <h1>التقرير الطبي الشامل</h1>
            <h3>نظام إدارة الأمراض المزمنة</h3>
        </div>
        
        <div class="header-info row">
            <div class="col-md-6">
                <p><strong>رقم التقرير:</strong> <?php echo sprintf('RPT-%06d', $patient_id); ?></p>
                <p><strong>تاريخ التقرير:</strong> <?php echo $report_date; ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <p><strong>المسؤول عن التقرير:</strong> <?php echo htmlspecialchars($admin_name); ?></p>
            </div>
        </div>
        
        <div class="patient-info">
            <h3>معلومات المريض</h3>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>الاسم:</strong> <?php echo htmlspecialchars($patient['name']); ?></p>
                    <p><strong>رقم المريض:</strong> <?php echo sprintf('PT-%06d', $patient['patient_id']); ?></p>
                    <p><strong>تاريخ الميلاد:</strong> <?php echo htmlspecialchars($patient['date_of_birth']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>الجنس:</strong> <?php echo htmlspecialchars($patient['gender']); ?></p>
                    <p><strong>رقم الهاتف:</strong> <?php echo htmlspecialchars($patient['contact_number']); ?></p>
                    <p><strong>البريد الإلكتروني:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="section-title">
            <h4>الأطباء المعالجين</h4>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>اسم الطبيب</th>
                    <th>التخصص</th>
                    <th>رقم الهاتف</th>
                    <th>البريد الإلكتروني</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($doctors_result->num_rows > 0): ?>
                    <?php while($doctor = $doctors_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                            <td><?php echo htmlspecialchars($doctor['specialty']); ?></td>
                            <td><?php echo htmlspecialchars($doctor['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">لا يوجد أطباء مسجلين لهذا المريض</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="section-title">
            <h4>السجلات الطبية</h4>
        </div>
        <table class="table table-bordered">
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
        
        <div class="page-break"></div>
        
        <div class="section-title">
            <h4>الأدوية الموصوفة</h4>
        </div>
        <table class="table table-bordered">
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
                            <td><?php echo htmlspecialchars($prescription['prescribed_date'] ?? ''); ?></td>
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
        
        <div class="section-title">
            <h4>نتائج الاختبارات المخبرية</h4>
        </div>
        <table class="table table-bordered">
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
        
        <div class="report-footer">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ملاحظة:</strong> هذا التقرير سري ومخصص للاستخدام الطبي فقط</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p><strong>تاريخ الطباعة:</strong> <?php echo date("Y-m-d H:i:s"); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-print when the page loads
        window.onload = function() {
            // Uncomment the line below to automatically print when page loads
            // window.print();
        };
    </script>
</body>
</html>