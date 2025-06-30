<?php
include 'db.php';
session_start();

// التحقق من تسجيل دخول الدكتور
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$patient_id = $_GET['patient_id'];

// استرجاع بيانات الدكتور من قاعدة البيانات
$sql = "SELECT name FROM doctors WHERE doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// التحقق من أن المريض مسجل مع هذا الدكتور
$sql_check = "SELECT * FROM patient_doctors WHERE patient_id = ? AND doctor_id = ?";
$check_stmt = $conn->prepare($sql_check);
$check_stmt->bind_param("ii", $patient_id, $doctor_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    $_SESSION['error'] = "عذراً، هذا المريض غير مسجل معك";
    header("Location: search_patient.php");
    exit();
}

// جلب بيانات المريض
$sql_patient = "SELECT * FROM patients WHERE patient_id = ?";
$patient_stmt = $conn->prepare($sql_patient);
$patient_stmt->bind_param("i", $patient_id);
$patient_stmt->execute();
$patient_result = $patient_stmt->get_result();
$patient = $patient_result->fetch_assoc();

// جلب التاريخ الطبي للمريض
$sql_records = "SELECT m.*, d.name as doctor_name 
                FROM medicalrecords m 
                LEFT JOIN doctors d ON m.doctor_id = d.doctor_id 
                WHERE m.patient_id = ?";
$records_stmt = $conn->prepare($sql_records);
$records_stmt->bind_param("i", $patient_id);
$records_stmt->execute();
$records_result = $records_stmt->get_result();

// جلب الأدوية الموصوفة للمريض
$sql_prescriptions = "SELECT p.*, d.name as doctor_name 
                     FROM prescriptions p 
                     LEFT JOIN doctors d ON p.doctor_id = d.doctor_id 
                     WHERE p.patient_id = ?";
$prescriptions_stmt = $conn->prepare($sql_prescriptions);
$prescriptions_stmt->bind_param("i", $patient_id);
$prescriptions_stmt->execute();
$prescriptions_result = $prescriptions_stmt->get_result();

// جلب الأشعة للمريض
$sql_xrays = "SELECT px.*, xt.xray_name, xt.description, xt.radiation_level, xt.required_preparation, d.name as doctor_name 
              FROM patient_xrays px 
              JOIN xraytypes xt ON px.xray_type_id = xt.xray_type_id
              LEFT JOIN doctors d ON px.doctor_id = d.doctor_id
              WHERE px.patient_id = ?";
$xray_stmt = $conn->prepare($sql_xrays);
$xray_stmt->bind_param("i", $patient_id);
$xray_stmt->execute();
$xray_result = $xray_stmt->get_result();

// جلب المواعيد السابقة للمريض مع الدكتور الحالي فقط
$sql_past_appointments = "SELECT a.*, d.name as doctor_name 
                         FROM appointments a 
                         LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                         WHERE a.patient_id = ? 
                         AND a.doctor_id = ?
                         AND a.appointment_date < CURDATE()
                         ORDER BY a.appointment_date DESC";
$past_appointments_stmt = $conn->prepare($sql_past_appointments);
$past_appointments_stmt->bind_param("ii", $patient_id, $doctor_id);
$past_appointments_stmt->execute();
$past_appointments_result = $past_appointments_stmt->get_result();

// جلب المواعيد القادمة للمريض مع الدكتور الحالي فقط
$sql_upcoming_appointments = "SELECT a.*, d.name as doctor_name 
                            FROM appointments a 
                            LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                            WHERE a.patient_id = ? 
                            AND a.doctor_id = ?
                            AND a.appointment_date >= CURDATE()
                            ORDER BY a.appointment_date ASC";
$upcoming_appointments_stmt = $conn->prepare($sql_upcoming_appointments);
$upcoming_appointments_stmt->bind_param("ii", $patient_id, $doctor_id);
$upcoming_appointments_stmt->execute();
$upcoming_appointments_result = $upcoming_appointments_stmt->get_result();

// جلب الاختبارات المخبرية للمريض
$sql_labtests = "SELECT l.*, lt.test_name AS test_type, lt.description, lt.normal_range, lt.unit, d.name as doctor_name 
                 FROM labtests l 
                 JOIN labtesttypes lt ON l.test_type_id = lt.test_type_id 
                 LEFT JOIN doctors d ON l.doctor_id = d.doctor_id
                 WHERE l.patient_id = ?";
$labtests_stmt = $conn->prepare($sql_labtests);
$labtests_stmt->bind_param("i", $patient_id);
$labtests_stmt->execute();
$labtests_result = $labtests_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات المريض</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@300;400&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
        <style>
        body {
            font-family: 'Changa', sans-serif;
            font-weight: 300;
        }
        .btn-login {
            background-color: #041951;
            color: white;
            font-weight: bold;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-login:hover {
            background-color: #041951;
            transform: scale(1.05);
        }
        .table {
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .table thead {
            background-color: #041951;
            color: white;
        }
        .btn-primary {
            background-color: #041951;
            border-color: #041951;
            margin: 5px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #041951;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1, h3 {
            color: #041951;
            margin: 30px 0 20px 0;
            border-bottom: 2px solid #041951;
            padding-bottom: 10px;
        }
    </style>
</head>
<body class="bg-light">
<!-- <div class="header">
      <div class="logo-box">
          <i class="fas fa-heartbeat text-danger fa-2x"></i>
          <span class="site-name">الأمراض المزمنة</span>
      </div>
      <nav>
          <a href="#" class="nav-link d-inline mx-3 text-white"><i class="fas fa-home"></i> الرئيسية</a>
          <a href="#" class="nav-link d-inline mx-3 text-white"><i class="fas fa-newspaper"></i> المقالات</a>
          <a href="#" class="nav-link d-inline mx-3 text-white"><i class="fas fa-concierge-bell"></i> الخدمات</a>
          <a href="#" class="nav-link d-inline mx-3 text-white"><i class="fas fa-envelope"></i> اتصل بنا</a>
      </nav>
      <?php 
   
      if (isset($_SESSION['email'])): ?>
      <div class="d-flex align-items-center">
       
          <div class="logo-box"><i class="fas fa-user text-primary fa-2x"></i> <?php echo $_SESSION['email']; ?></div>
            <?php if (isset($_SESSION['doctor_id'])): ?>
            <a href="doctor_profile.php" class="btn btn-login mx-2">الملف الشخصي للطبيب</a>
            
            <?php else: ?>
            <a href="profile.php" class="btn btn-login mx-2">الملف الشخصي</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-login mx-2">تسجيل الخروج</a>
      </div>
      <?php else: ?>
      <div class="d-flex align-items-center">
          <a href="login.php" class="btn btn-login mx-2">تسجيل الدخول</a>
          <div class="logo-box"><i class="fas fa-user text-primary fa-2x"></i></div>
      </div>
      <?php endif; ?>
  </div> -->
    <nav class="nav" id="nav">
    <div class="nav-menu nav-container" id="nav-menu">
      <div class="nav-shape"></div>
      <div class="nav-close" id="nav-close">
        <i class="bx bx-x"></i>
      </div>
      <div class="nav-data mt-5">
        <!-- <div class="nav-mask">
          <img src="https://i.postimg.cc/PqfBmCCM/protfolio_img.jpg" alt="" class="nav-img" />
        </div> -->
        <span class="nav-greeting">مرحبًا</span>
        <h1 class="nav-name">
        <?php echo htmlspecialchars($doctor['name']); ?>
        </h1>
      </div>
      <ul class="nav-list">
        <li class="nav-item">
          <a href="index.php #home" class="nav-link active-link">
            <i class="bx bx-home"></i> الرئيسية
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php #about" class="nav-link">
            <i class="bx bx-user"></i> الهدف
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php #services" class="nav-link">
            <i class="bx bx-briefcase-alt-2"></i> الخدمات
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php #tips" class="nav-link">
            <i class="bx bx-bookmark"></i> وصفات طبية
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php #contact" class="nav-link">
            <i class="bx bx-message-square-detail"></i> تواصل معنا
          </a>
        </li>
        <?php if (isset($_SESSION['email'])): ?>
        <li class="nav-item">
          <a href="logout.php" class="nav-link">
            <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>

  <header class="header" id="header">
      <nav class="header-nav container">
        <div class="logo-box">
          <i class="fa-solid fa-disease text-danger fa-2x"></i>
          <span class="header-logo site-name">Electronic Vital</span>
        </div>
        <div class="d-flex align-items-center">
          <?php if (isset($_SESSION['email'])): ?>
            <?php if (isset($_SESSION['doctor_id'])): ?>
              <!-- <a href="doctor_profile.php" class="btn btn-login mx-2" style="position: relative; left: 65rem;">الملف الشخصي للطبيب</a> -->
              <a href="search_patient.php" class="btn btn-login mx-2" style="position: relative; left: 67.5rem;">البحث عن مريض</a>
            <?php else: ?>
              <!-- <a href="profile.php" class="btn btn-login mx-2">الملف الشخصي</a> -->
            <?php endif; ?>
          <?php else: ?>
            <a href="login.php" class="btn btn-login mx-2" style="position: relative; left: 71.5rem;">LOG IN</a>
          <?php endif; ?>          
          <div class="header-toggle" id="header-toggle">
            <i class="bx bx-grid-alt"></i>
          </div>
        </div>
      </nav>
    </header>

  <br>
    <br>
    <div class="container mt-5">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
  <br>
    <br>
    <div class="container mt-5">
        <h1>بيانات المريض</h1>
        <p><strong>الاسم:</strong> <?php echo $patient['name']; ?></p>
        <p><strong>تاريخ الميلاد:</strong> <?php echo $patient['date_of_birth']; ?></p>
        <p><strong>الجنس:</strong> <?php echo $patient['gender']; ?></p>

        <h3>التاريخ الطبي</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>التشخيص</th>
                    <th>العلاج</th>
                    <th>اسم الطبيب</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $records_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['diagnosis']; ?></td>
                        <td><?php echo $row['treatment']; ?></td>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td>
                            <a href="edit_medical_record.php?id=<?php echo $row['record_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> تعديل</a>
                            <a href="delete_medical_record.php?id=<?php echo $row['record_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا السجل؟')"><i class="fas fa-trash"></i> حذف</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>الأدوية الموصوفة</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>الدواء</th>
                    <th>الجرعة</th>
                    <th>التعليمات</th>
                    <th>اسم الطبيب</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $prescriptions_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['medicine_name']; ?></td>
                        <td><?php echo $row['dosage']; ?></td>
                        <td><?php echo $row['instructions']; ?></td>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td>
                            <a href="edit_prescription.php?id=<?php echo $row['prescription_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> تعديل</a>
                            <a href="delete_prescription.php?id=<?php echo $row['prescription_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الدواء؟')"><i class="fas fa-trash"></i> حذف</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>الاختبارات المخبرية</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>اسم الاختبار</th>
                    <th>الوصف</th>
                    <th>النطاق الطبيعي</th>
                    <th>الوحدات</th>
                    <th>النتيجة</th>
                    <th>اسم الطبيب</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $labtests_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['test_type']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['normal_range']; ?></td>
                        <td><?php echo $row['unit']; ?></td>
                        <td><?php echo $row['result']; ?></td>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td>
                            <a href="edit_labtest.php?id=<?php echo $row['test_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> تعديل</a>
                            <a href="delete_labtest.php?id=<?php echo $row['test_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الاختبار؟')"><i class="fas fa-trash"></i> حذف</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>المواعيد السابقة</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>تاريخ الموعد</th>
                    <th>الملاحظات</th>
                    <th>اسم الطبيب</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $past_appointments_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['appointment_date']; ?></td>
                        <td><?php echo $row['notes']; ?></td>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td>
                            <a href="edit_appointment.php?id=<?php echo $row['appointment_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> تعديل</a>
                            <a href="delete_appointment.php?id=<?php echo $row['appointment_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')"><i class="fas fa-trash"></i> حذف</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>المواعيد القادمة</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>تاريخ الموعد</th>
                    <th>الملاحظات</th>
                    <th>اسم الطبيب</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $upcoming_appointments_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['appointment_date']; ?></td>
                        <td><?php echo $row['notes']; ?></td>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td>
                            <a href="edit_appointment.php?id=<?php echo $row['appointment_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> تعديل</a>
                            <a href="delete_appointment.php?id=<?php echo $row['appointment_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')"><i class="fas fa-trash"></i> حذف</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>الأشعة</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>نوع الأشعة</th>
                    <th>الوصف</th>
                    <th>مستوى الإشعاع</th>
                    <th>التحضير المطلوب</th>
                    <th>تاريخ الأشعة</th>
                    <th>الملاحظات</th>
                    <th>اسم الطبيب</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $xray_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['xray_name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['radiation_level']; ?></td>
                        <td><?php echo $row['required_preparation']; ?></td>
                        <td><?php echo $row['test_date']; ?></td>
                        <td><?php echo $row['notes']; ?></td>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td>
                            <a href="edit_xray.php?id=<?php echo $row['patient_xray_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> تعديل</a>
                            <a href="delete_xray.php?id=<?php echo $row['patient_xray_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذه الأشعة؟')"><i class="fas fa-trash"></i> حذف</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="add_medical_record.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-primary">اضافة سجل طبي</a>
        <a href="add_prescription.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-primary">اضافة دواء</a>
        <a href="add_xray.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-primary">اضافة اشعة</a>
        <!-- <a href="add_appointment.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-primary">اضافة موعد</a> -->
        <a href="add_labtest.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-primary">اضافة التحليل</a>
    </div>
    <script src="js/main.js"></script>

</body>
</html>
