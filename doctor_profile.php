<?php
include 'db.php'; // الاتصال بقاعدة البيانات
session_start(); // بدء الجلسة
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول إذا لم يكن مسجلاً الدخول
    exit;
}
// جلب بيانات الدكتور باستخدام ID الدكتور
$doctor_id = $_SESSION['doctor_id']; // الحصول على ID الدكتور من الجلسة
if (!isset($doctor_id)) {
    echo "لا يوجد دكتور مسجل الدخول!";
    exit;
}
$sql_doctor = "SELECT * FROM doctors WHERE doctor_id = $doctor_id";
$result_doctor = $conn->query($sql_doctor);
if ($result_doctor->num_rows > 0) {
    $doctor = $result_doctor->fetch_assoc();
} else {
    echo "لا يوجد دكتور بهذا الرقم!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بروفايل الدكتور</title>
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
            border: none;
            padding: 12px 36px;
            border-radius: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);

        }

            .btn-login:hover {
            background-color: #8b9bb0;
            transform: scale(1.05);
        }

        .profile-header {
            background-color: #041951;
            color: white;
            padding: 20px;
            border-radius: 8px;
        }

    </style>
</head>
<body class="bg-light">
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
        <!-- Profile Header -->
        <div class="profile-header text-center">
        <h1 class="text-center" style="color: white">بروفايل الدكتور</h1>
        <p class="text-center">يمكنك الاطلاع على معلومات الدكتور الخاصة بك أدناه.</p>
    </div>
    <div class="container mt-5">
        <div class="card">
        <div class="card-header text-white" style="background-color: #041951;text-align: center" >
        <h4 style=" margin: 0; padding: 6px;">البيانات الشخصية</h4>
        </div>
            <div class="card-body">
                <p><strong>الاسم:</strong> <?php echo $doctor['name']; ?></p>
                <p><strong>التخصص:</strong> <?php echo $doctor['specialty']; ?></p>
                <p><strong>رقم الاتصال:</strong> <?php echo $doctor['contact_number']; ?></p>
                <p><strong>البريد الإلكتروني:</strong> <?php echo $doctor['email']; ?></p>
                <p><strong>المستشفى:</strong> <?php echo $doctor['hospital_affiliation']; ?></p>
                <p><strong>المدينة:</strong> <?php echo $doctor['address']; ?></p>
                <p><strong>الخبرة:</strong> <?php echo $doctor['experience']; ?> سنوات</p>
                <p><strong>السيرة الذاتية:</strong> <?php echo nl2br($doctor['qualification']); ?></p>
                
            </div>
        </div>
    </div>
    <!-- قسم إدارة المواعيد -->
    <div class="card mt-4">
    <div class="card-header text-white" style="background-color: #041951;text-align: center" >
    <h4 style=" margin: 0; padding: 6px;">إدارة المواعيد</h4>
        </div>
        <div class="card-body">
            <h5 class="mb-3">المواعيد المحجوزة</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>اسم المريض</th>
                        <th>التاريخ</th>
                        <th>الوقت</th>
                        <th>الملاحظات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_pending = "SELECT a.*, p.name as patient_name 
                                   FROM appointments a 
                                   JOIN patients p ON a.patient_id = p.patient_id 
                                   WHERE a.doctor_id = $doctor_id AND a.status = 'Scheduled'
                                   ORDER BY a.appointment_date ASC";
                    $pending_result = $conn->query($sql_pending);
                    while($appointment = $pending_result->fetch_assoc()): 
                        $appointment_date = date('Y-m-d', strtotime($appointment['appointment_date']));
                        $appointment_time = date('H:i', strtotime($appointment['appointment_date']));
                    ?>
                        <tr>
                            <td><?php echo $appointment['patient_name']; ?></td>
                            <td><?php echo $appointment_date; ?></td>
                            <td><?php echo $appointment_time; ?></td>
                            <td><?php echo $appointment['notes']; ?></td>
                            <td>
                                <form action="approve_appointment.php" method="POST" class="d-inline">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                                    <button type="submit" name="action" value="Completed" class="btn btn-success btn-sm">قبول</button>
                                    <button type="submit" name="action" value="Cancelled" class="btn btn-danger btn-sm">رفض</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h5 class="mt-4 mb-3">المواعيد المقبولة</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>اسم المريض</th>
                        <th>التاريخ</th>
                        <th>الوقت</th>
                        <th>الملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                        <?php
                        $sql_approved = "SELECT a.*, p.name as patient_name 
                                        FROM appointments a 
                                        JOIN patients p ON a.patient_id = p.patient_id 
                                        WHERE a.doctor_id = $doctor_id AND a.status = 'Scheduled'
                                        ORDER BY a.appointment_date ASC";
                        $approved_result = $conn->query($sql_approved);
                        while($appointment = $approved_result->fetch_assoc()): 
                            $appointment_date = date('Y-m-d', strtotime($appointment['appointment_date']));
                            $appointment_time = date('H:i', strtotime($appointment['appointment_date']));
                        ?>
                            <tr>
                                <td><?php echo $appointment['patient_name']; ?></td>
                                <td><?php echo $appointment_date; ?></td>
                                <td><?php echo $appointment_time; ?></td>
                                <td><?php echo $appointment['notes']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-4">
    <div class="card-header text-white" style="background-color: #041951;text-align: center" >
    <h4 style="margin: 0; padding: 6px;">المرضى المسجلين</h4>
            </div>
            <div class="card-body">
                <!-- عرض المرضى الحاليين -->
                <h5 class="mb-3">المرضى الحاليين</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>اسم المريض</th>
                            <th>رقم الملف</th>
                            <th>تاريخ التسجيل</th>
                         
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql_my_patients = "SELECT p.*, pd.registration_date 
                                         FROM patients p 
                                         JOIN patient_doctors pd ON p.patient_id = pd.patient_id 
                                         WHERE pd.doctor_id = $doctor_id";
                        $my_patients_result = $conn->query($sql_my_patients);
                        while($patient = $my_patients_result->fetch_assoc()): 
                        ?>
                            <tr>
                                <td><?php echo $patient['name']; ?></td>
                                <td><?php echo $patient['patient_id']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($patient['registration_date'])); ?></td>
                             
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- نموذج إضافة مريض جديد -->
                <h5 class="mt-4 mb-3">إضافة مريض جديد</h5>
                <form action="add_patient_to_doctor.php" method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label for="new_patient" class="form-label">اختر المريض:</label>
                        <select class="form-select" id="new_patient" name="patient_id" required>
                            <option value="">اختر المريض...</option>
                            <?php
                            // جلب قائمة المرضى الذين لم يتم تسجيلهم مع الطبيب بعد
                            $sql_available_patients = "SELECT * FROM patients p 
                                                   WHERE p.patient_id NOT IN (
                                                       SELECT patient_id FROM patient_doctors 
                                                       WHERE doctor_id = $doctor_id
                                                   )";
                            $available_patients_result = $conn->query($sql_available_patients);
                            while($patient = $available_patients_result->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $patient['patient_id']; ?>">
                                    <?php echo $patient['name'] . ' (رقم الملف: ' . $patient['patient_id'] . ')'; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn" style="background-color: #041951;color: white">إضافة المريض</button>
                    </div>
                </form>
            </div>
        </div>

    <!-- قسم تحديد أوقات العمل -->
    <div class="card mt-4 mb-4">
    <div class="card-header text-white" style="background-color: #041951;text-align: center" >
    <h4 style="margin: 0; padding: 6px;">تحديد أوقات العمل</h4>
        </div>
        <div class="card-body">
            <form action="update_working_hours.php" method="POST">
                <div class="row">
                    <?php
                    $days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
                    $sql_hours = "SELECT * FROM doctor_working_hours WHERE doctor_id = $doctor_id";
                    $hours_result = $conn->query($sql_hours);
                    $working_hours = [];
                    while($hour = $hours_result->fetch_assoc()) {
                        $working_hours[$hour['day']] = [
                            'start_time' => $hour['start_time'],
                            'end_time' => $hour['end_time'],
                            'is_working' => $hour['is_working']
                        ];
                    }

                    foreach($days as $index => $day):
                        $current_hours = isset($working_hours[$index]) ? $working_hours[$index] : ['start_time' => '09:00', 'end_time' => '17:00', 'is_working' => 0];
                    ?>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="working_<?php echo $index; ?>" 
                                           name="working_days[<?php echo $index; ?>]" value="1" 
                                           <?php echo $current_hours['is_working'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="working_<?php echo $index; ?>"><?php echo $day; ?></label>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">وقت البدء:</label>
                                        <input type="time" class="form-control" name="start_time[<?php echo $index; ?>]" 
                                               value="<?php echo $current_hours['start_time']; ?>">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">وقت الانتهاء:</label>
                                        <input type="time" class="form-control" name="end_time[<?php echo $index; ?>]" 
                                               value="<?php echo $current_hours['end_time']; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn- mt-3" style="background-color: #041951;text-align: center;color: white;" >حفظ أوقات العمل</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
