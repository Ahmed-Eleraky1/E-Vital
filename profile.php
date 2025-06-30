<?php
include 'db.php'; // الاتصال بقاعدة البيانات
session_start(); // بدء جلسة العمل

// جلب معلومات المريض باستخدام ID المريض
if (!isset($_SESSION['patient_id']) || !is_numeric($_SESSION['patient_id'])) {
    echo "Invalid patient ID!";
    exit;
}

$patient_id = intval($_SESSION['patient_id']); // الحصول على ID المريض من الجلسة وتأكيد أنه رقم صحيح

$sql = "SELECT * FROM patients WHERE patient_id = $patient_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $patient = $result->fetch_assoc();
    $_SESSION['patient_id'] = $patient['patient_id']; // تعيين ID المريض في الجلسة
} else {
    echo "No patient found!";
    exit;
}

// جلب التاريخ الطبي للمريض
$sql_records = "SELECT * FROM medicalrecords WHERE patient_id = $patient_id";
$records_result = $conn->query($sql_records);

// جلب الأدوية الموصوفة للمريض
$sql_prescriptions = "SELECT * FROM prescriptions WHERE patient_id = $patient_id";
$prescriptions_result = $conn->query($sql_prescriptions);

// جلب الأشعة للمريض من جدول patient_xrays وربطها مع xraytypes
$sql_xrays = "SELECT patient_xrays.*, xraytypes.xray_name, xraytypes.description, xraytypes.radiation_level, xraytypes.required_preparation 
              FROM patient_xrays 
              JOIN xraytypes ON patient_xrays.xray_type_id = xraytypes.xray_type_id 
              WHERE patient_xrays.patient_id = $patient_id";
$xray_result = $conn->query($sql_xrays);



// جلب المواعيد السابقة للمريض
$sql_past_appointments = "SELECT * FROM appointments 
                        WHERE patient_id = $patient_id 
                        AND appointment_date < CURDATE()
                        ORDER BY appointment_date DESC";
$past_appointments_result = $conn->query($sql_past_appointments);

// جلب المواعيد القادمة للمريض
$sql_upcoming_appointments = "SELECT * FROM appointments 
                           WHERE patient_id = $patient_id 
                           AND appointment_date >= CURDATE()
                           ORDER BY appointment_date ASC";
$upcoming_appointments_result = $conn->query($sql_upcoming_appointments);

// جلب الاختبارات المخبرية للمريض
$sql_labtests = "SELECT labtests.*, labtesttypes.test_name AS test_name, labtesttypes.description, labtesttypes.normal_range, labtesttypes.unit 
                 FROM labtests 
                 JOIN labtesttypes ON labtests.test_type_id = labtesttypes.test_type_id 
                 WHERE labtests.patient_id = $patient_id";
$labtests_result = $conn->query($sql_labtests);

// جلب قائمة الأطباء
$sql_doctors = "SELECT doctor_id, name, specialty FROM doctors";
$doctors_result = $conn->query($sql_doctors);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بروفايل المريض</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@300;400&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
    <style>
        body {
            font-family: 'Changa', sans-serif;
        }
        .btn-login {
            background-color: #ffcc00;
            color: black;
            font-weight: bold;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-login:hover {
            background-color: #e6b800;
            transform: scale(1.05);
        }

        .profile-header {
            background-color: #041951;
            color: white;
            padding: 20px;
            border-radius: 8px;
        }
        .card {
            border-radius: 8px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table {
            border-radius: 8px;
        }
        .table-striped tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        /* تصميم الإشعارات */
        .notifications-icon {
            position: relative;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .notifications-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ffcc00;
            color: black;
            font-size: 0.75rem;
            font-weight: bold;
            border-radius: 50%;
            padding: 2px 6px;
            display: none;
        }
        .notification-item {
            border-bottom: 1px solid #eee;
        }
        .notification-item:last-child {
            border-bottom: none;
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
        <?php echo htmlspecialchars($patient['name']); ?>
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
              <!-- <a href="doctor_profile.php" class="btn btn-login mx-2">الملف الشخصي للطبيب</a> -->
            <?php else: ?>
              <!-- <a href="profile.php" class="btn btn-login mx-2">الملف الشخصي</a> -->
            <?php endif; ?>
          <?php else: ?>
            <a href="login.php" class="btn btn-login mx-2" style="position: relative; left: 71.5rem;">LOG IN</a>
          <?php endif; ?>

          <!-- زرار الإشعارات -->
          <div class="dropdown mx-2" style="position: relative; left: 71.5rem;">
            <span class="notifications-icon" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-bell" style="color: #041951;"></i>
              <span class="notifications-count">0</span>
            </span>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown" style="width: 300px; max-height: 400px; overflow-y: auto;">
              <div class="notifications-list p-2">
                <!-- الإشعارات هتتحدث هنا -->
              </div>
            </div>
          </div>

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
            <h2 style="color: white">بروفايل المريض</h2>
            <p class="lead">مرحبًا بك في ملفك الطبي، هنا يمكنك عرض بياناتك الشخصية وتاريخك الطبي</p>
        </div>

        <!-- Patient Info Section -->
        <div class="card mt-4">
            <div class="card-header text-white" style="background-color: #041951;text-align: center" >
                <h4 style=" margin: 0; padding: 6px;">البيانات الشخصية</h4>
            </div>
            <div class="card-body">
                <p><strong>الاسم:</strong> <?php echo $patient['name']; ?></p>
                <p><strong>تاريخ الميلاد:</strong> <?php echo $patient['date_of_birth']; ?></p>
                <p><strong>الجنس:</strong> <?php echo $patient['gender']; ?></p>
                <p><strong>رقم الاتصال:</strong> <?php echo $patient['contact_number']; ?></p>
                <p><strong>البريد الإلكتروني:</strong> <?php echo $patient['email']; ?></p>
                <p><strong>العنوان:</strong> <?php echo $patient['address'] ?: 'لا يوجد عنوان'; ?></p>
            </div>
        </div>

        <!-- قسم الأطباء المعالجين بعد بيانات المريض الشخصية مباشرة -->
        <div class="card mt-4">
            <div class="card-header text-white" style="background-color: #041951;text-align: center" >
                <h4 style=" margin: 0; padding: 6px;">الأطباء المعالجين</h4>
            </div>
            <div class="card-body">
                <!-- عرض الأطباء الحاليين -->
                <h5 class="mb-3">الأطباء الحاليين</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>اسم الطبيب</th>
                            <th>التخصص</th>
                            <th>مكان العمل</th>
                            <th>تاريخ التسجيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql_my_doctors = "SELECT d.*, pd.registration_date 
                                         FROM doctors d 
                                         JOIN patient_doctors pd ON d.doctor_id = pd.doctor_id 
                                         WHERE pd.patient_id = $patient_id";
                        $my_doctors_result = $conn->query($sql_my_doctors);
                        while($doctor = $my_doctors_result->fetch_assoc()): 
                        ?>
                            <tr>
                                <td><?php echo $doctor['name']; ?></td>
                                <td><?php echo $doctor['specialty']; ?></td>
                                <td><?php echo $doctor['hospital_affiliation']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($doctor['registration_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Medical Records Section -->
        <div class="card mt-4">
            <div class="card-header text-white" style="background-color: #041951;text-align: center">
                <h4 style=" margin: 0; padding: 6px;">التاريخ الطبي</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>التشخيص</th>
                            <th>العلاج</th>
                            <th>الوصفة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $records_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['diagnosis']; ?></td>
                                <td><?php echo $row['treatment']; ?></td>
                                <td><?php echo $row['prescription']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Prescriptions Section -->
        <div class="card mt-4">
            <div class="card-header text-white" style="background-color: #041951;text-align: center">
                <h4 style=" margin: 0; padding: 6px;">الأدوية الموصوفة</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>الدواء</th>
                            <th>الجرعة</th>
                            <th>التعليمات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $prescriptions_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['medicine_name']; ?></td>
                                <td><?php echo $row['dosage']; ?></td>
                                <td><?php echo $row['instructions']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- الأشعة -->
         <div class="card mt-4">
            <div class="card-header text-white" style="background-color: #041951;text-align: center">
                <h4 style=" margin: 0; padding: 6px;">الأشعة</h4>
            </div>
            <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>اسم الأشعة</th>
                    <th>الوصف</th>
                    <th>مستوى الإشعاع</th>
                    <th>التحضير المطلوب</th>
                    <th>تاريخ الأشعة</th>
                    <th>الملاحظات</th>
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
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

 
     <!-- الاختبارات المخبرية -->
     <div class="card mt-4">
        <div class="card-header text-white" style="background-color: #041951;text-align: center">
            <h4 style=" margin: 0; padding: 6px;">الاختبارات المخبرية</h4>
        </div>
        <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>اسم الاختبار</th>
                    <th>الوصف</th>
                    <th>النطاق الطبيعي</th>
                    <th>الوحدات</th>
                    <th>النتيجة</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $labtests_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['test_name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['normal_range']; ?></td>
                        <td><?php echo $row['unit']; ?></td>
                        <td><?php echo $row['result']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
    <!-- Appointments Section -->
    <div class="card mt-4">
        <div class="card-header text-white" style="background-color: #041951;text-align: center">
            <h4 style=" margin: 0; padding: 6px;">مواعيد المريض</h4>
        </div>
        <div class="card-body">
            <!-- نموذج حجز موعد جديد -->
            <div class="mb-4">
                <h5 class="text-primary mb-3">حجز موعد جديد</h5>
                <form action="book_appointment.php" method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label for="doctor" class="form-label">اختر الطبيب:</label>
                        <select class="form-select" id="doctor" name="doctor_id" required>
                            <option value="">اختر الطبيب...</option>
                            <?php while($doctor = $doctors_result->fetch_assoc()): ?>
                                <option value="<?php echo $doctor['doctor_id']; ?>">
                                    <?php echo $doctor['name'] . ' - ' . $doctor['specialty']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="appointment_date" class="form-label">تاريخ الموعد:</label>
                        <input type="date" class="form-control" id="appointment_date" required>
                    </div>
                    <div class="col-md-6">
                        <label for="available_slots" class="form-label">المواعيد المتاحة:</label>
                        <select class="form-select" id="available_slots" name="appointment_date" required disabled>
                            <option value="">اختر الموعد المناسب...</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label">ملاحظات:</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn" style="background-color: #041951;color: white;">حجز الموعد</button>
                    </div>
                </form>

                <script>
                document.getElementById('doctor').addEventListener('change', function() {
                    const appointmentDate = document.getElementById('appointment_date');
                    const availableSlots = document.getElementById('available_slots');
                    
                    if (this.value) {
                        appointmentDate.disabled = false;
                    } else {
                        appointmentDate.disabled = true;
                        availableSlots.disabled = true;
                        availableSlots.innerHTML = '<option value="">اختر الموعد المناسب...</option>';
                    }
                });

                document.getElementById('appointment_date').addEventListener('change', function() {
                    const doctorId = document.getElementById('doctor').value;
                    const availableSlots = document.getElementById('available_slots');
                    
                    if (this.value && doctorId) {
                        // جلب المواعيد المتاحة
                        fetch(`get_available_slots.php?doctor_id=${doctorId}&date=${this.value}`)
                            .then(response => response.json())
                            .then(data => {
                                availableSlots.innerHTML = '<option value="">اختر الموعد المناسب...</option>';
                                
                                if (data.available_slots && data.available_slots.length > 0) {
                                    data.available_slots.forEach(slot => {
                                        const option = document.createElement('option');
                                        option.value = `${this.value} ${slot}:00`;
                                        option.textContent = slot;
                                        availableSlots.appendChild(option);
                                    });
                                    availableSlots.disabled = false;
                                } else {
                                    availableSlots.innerHTML = '<option value="">لا توجد مواعيد متاحة</option>';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                availableSlots.innerHTML = '<option value="">حدث خطأ في جلب المواعيد</option>';
                            });
                    } else {
                        availableSlots.disabled = true;
                        availableSlots.innerHTML = '<option value="">اختر الموعد المناسب...</option>';
                    }
                });
                </script>
            </div>

            <!-- المواعيد القادمة -->
            <div class="card mt-4">
        <div class="card-header text-white" style="background-color: #041951;text-align: center">
            <h4 style=" margin: 0; padding: 6px;">المواعيد القادمة </h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>تاريخ الموعد</th>
                        <th>الحالة</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($upcoming_appointments_result->num_rows > 0): ?>
                        <?php while($row = $upcoming_appointments_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['appointment_date']; ?></td>
                                <td><?php echo $row['status']; ?></td>
                                <td><?php echo $row['notes']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">لا توجد مواعيد قادمة</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </div>  

            <!-- المواعيد السابقة -->
            <div class="card mt-4">
        <div class="card-header text-white" style="background-color: #041951;text-align: center">
            <h4 style=" margin: 0; padding: 6px;">المواعيد السابقة </h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>تاريخ الموعد</th>
                        <th>الحالة</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($past_appointments_result->num_rows > 0): ?>
                        <?php while($row = $past_appointments_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['appointment_date']; ?></td>
                                <td><?php echo $row['status']; ?></td>
                                <td><?php echo $row['notes']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">لا توجد مواعيد سابقة</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>

    <!-- JavaScript بتاع الإشعارات -->
    <script>
    // دالة لتحديث الإشعارات
    function updateNotifications() {
        fetch('get_notifications.php')
            .then(response => response.json())
            .then(data => {
                const notificationsList = document.querySelector('.notifications-list');
                const notificationsCount = document.querySelector('.notifications-count');
                
                // تحديث عدد الإشعارات غير المقروءة
                if (data.unread_count > 0) {
                    notificationsCount.style.display = 'block';
                    notificationsCount.textContent = data.unread_count;
                } else {
                    notificationsCount.style.display = 'none';
                }

                // تحديث قائمة الإشعارات
                notificationsList.innerHTML = '';
                if (data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        const notificationElement = document.createElement('div');
                        notificationElement.className = `notification-item p-2 border-bottom ${!notification.is_read ? 'bg-light' : ''}`;
                        
                        const createdAt = new Date(notification.created_at);
                        const timeString = createdAt.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });
                        const dateString = createdAt.toLocaleDateString('ar-SA');

                        notificationElement.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">${dateString} ${timeString}</small>
                                ${!notification.is_read ? '<span class="badge bg-primary">جديد</span>' : ''}
                            </div>
                            <div class="mt-1">${notification.message}</div>
                        `;
                        notificationsList.appendChild(notificationElement);
                    });
                } else {
                    notificationsList.innerHTML = '<div class="text-center p-3">لا توجد إشعارات</div>';
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // تحديث الإشعارات عند فتح القائمة
    document.getElementById('notificationsDropdown').addEventListener('click', function() {
        updateNotifications();
        // تحديث حالة الإشعارات كمقروءة
        fetch('get_notifications.php?mark_as_read=true')
            .then(response => response.json())
            .catch(error => console.error('Error:', error));
    });

    // تحديث الإشعارات كل دقيقة
    setInterval(updateNotifications, 60000);

    // تحديث الإشعارات عند تحميل الصفحة
    updateNotifications();
    </script>
</body>
</html>

<?php
$conn->close();
?>