
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

// Fetch admin's name
$admin_id = $_SESSION['admin_id'];
$sql_admin = "SELECT name FROM admins WHERE admin_id = '$admin_id'";
$admin_result = $conn->query($sql_admin);
$admin_name = $admin_result && $admin_result->num_rows > 0 ? $admin_result->fetch_assoc()['name'] : 'مدير';

// Get today's statistics
$today = date('Y-m-d');
$sql_today = "SELECT
    (SELECT COUNT(*) FROM appointments WHERE DATE(appointment_date) = '$today') as today_appointments,
    (SELECT COUNT(*) FROM appointments WHERE DATE(appointment_date) = '$today' AND status = 'completed') as completed_appointments,
    (SELECT COUNT(*) FROM appointments WHERE DATE(appointment_date) = '$today' AND status = 'pending') as pending_appointments,
    (SELECT COUNT(*) FROM doctors WHERE DATE(created_at) = '$today') as new_doctors,
    (SELECT COUNT(*) FROM patients WHERE DATE(created_at) = '$today') as new_patients";
$today_stats = $conn->query($sql_today)->fetch_assoc();

// Get alerts
$sql_alerts = "SELECT
    (SELECT COUNT(*) FROM appointments 
     WHERE appointment_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 DAY)
     AND status = 'Scheduled') as upcoming_appointments";
$alerts = $conn->query($sql_alerts)->fetch_assoc();

// Get recent activities
$sql_activities = "
(SELECT 
    'appointment' as type,
    appointment_id as id,
    CONCAT(p.name, ' حجز موعد مع د. ', d.name) as description,
    a.created_at as date
FROM appointments a
JOIN patients p ON a.patient_id = p.patient_id
JOIN doctors d ON a.doctor_id = d.doctor_id
ORDER BY a.created_at DESC
LIMIT 5)
UNION ALL
(SELECT 
    'doctor' as type,
    doctor_id as id,
    CONCAT('تم تسجيل طبيب جديد: ', name) as description,
    created_at as date
FROM doctors
ORDER BY created_at DESC
LIMIT 5)
UNION ALL
(SELECT 
    'patient' as type,
    patient_id as id,
    CONCAT('تم تسجيل مريض جديد: ', name) as description,
    created_at as date
FROM patients
ORDER BY created_at DESC
LIMIT 5)
ORDER BY date DESC
LIMIT 10";
$activities = $conn->query($sql_activities);

// Get upcoming appointments
$sql_upcoming = "SELECT 
    a.*,
    p.name as patient_name,
    p.contact_number as patient_contact_number,
    d.name as doctor_name,
    d.specialty as doctor_specialty
FROM appointments a
JOIN patients p ON a.patient_id = p.patient_id
JOIN doctors d ON a.doctor_id = d.doctor_id
WHERE a.appointment_date > NOW()
ORDER BY a.appointment_date ASC
LIMIT 5";
$upcoming = $conn->query($sql_upcoming);

// Get recent medical records
$sql_records = "SELECT 
    mr.*,
    p.name as patient_name,
    d.name as doctor_name
FROM medicalrecords mr
JOIN patients p ON mr.patient_id = p.patient_id
JOIN doctors d ON mr.doctor_id = d.doctor_id
ORDER BY mr.created_at DESC
LIMIT 5";
$records = $conn->query($sql_records);

// Get system status
$maintenance_mode = 0;
$sql_settings = "SELECT value FROM system_settings WHERE setting_key = 'maintenance_mode'";
$settings_result = $conn->query($sql_settings);
if ($settings_result && $row = $settings_result->fetch_assoc()) {
    $maintenance_mode = (int)$row['value'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - نظام إدارة المرضى المزمنين</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@300;400;500;700&display=swap" rel="stylesheet">


    <style>
        body {
        background: linear-gradient(to right, #d0e5ff, #e1f0ff);
        font-family: 'Changa', sans-serif;
        }
        .sidebar {
            background: linear-gradient(180deg, #1e3a8a 0%, #3b82f6 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .sidebar a {
            transition: all 0.2s ease;
            text-decoration: none;        }
        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(-5px);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;3
            color: white;
        
        }
        .table th, .table td {
            padding: 1rem;
        }
        .activity-item {
            background: #ffffff;
            border-right: 4px solid #3b82f6;
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);

        }
        .activity-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        .maintenance-alert {
            background: linear-gradient(90deg, #fef3c7, #fef9c3);
            border-radius: 8px;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        .card {
            padding: 30px;
            border-radius: 20px;
            background: white;
            text-align: center;
            color: black;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

    .title {
      font-size: 20px;
      margin-bottom: 20px;
    }

    .circle {
      position: relative;
      width: 150px;
      height: 150px;
      margin: 20px auto 20px;
    }

    .circle svg {
      transform: rotate(-90deg);
    }

    .circle circle {
      fill: none;
      stroke-width: 12;
      stroke-linecap: round;
    }

    .circle .bg {
    stroke: cornflowerblue;    }

    .circle .progress {
      stroke: #3b82f6;
      stroke-dasharray: 440;
      stroke-dashoffset: 440;
      transition: stroke-dashoffset 1s ease;
    }

    .percent {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 28px;
      font-weight: bold;
    }

    .subtitle {
      font-size: 16px;
      margin-top: 10px;
    }


    </style>
</head>
<body>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 fixed top-0 bottom-0 p-6 text-white">
            <h2 class="text-2xl font-bold mb-8 text-center"> <?php echo htmlspecialchars($admin_name); ?>
         </h2>
            <nav>
                <ul class="space-y-4">
                    <li>
                        <a href="dashboard.php" class="flex items-center p-3 rounded-lg text-white hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-home ml-2" ></i>الرئيسية
                        </a>
                    </li>

                    <li>
                        <a href="admin_profile.php" class="flex items-center p-3 rounded-lg text-white hover:bg-white hover:bg-opacity-10" >
                            <i class="fas fa-user-circle ml-2"></i> الملف الشخصي
                        </a>
                    </li>

                    <li>
                        <a href="manage_patients.php" class="flex items-center p-3 rounded-lg text-white hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-user-plus ml-2"></i> إدارة المرضى
                        </a>
                    </li>

                    <li>
                        <a href="manage_doctors.php" class="flex items-center p-3 rounded-lg text-white hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-user-md ml-2"></i> إدارة الأطباء
                        </a>
                    </li>

                    <li>
                        <a href="manage_appointments.php" class="flex items-center p-3 rounded-lg text-white hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-calendar-alt ml-2"></i> إدارة المواعيد
                        </a>
                    </li>

                    <li >
                         <a href="system_settings.php" class="flex items-center p-3 rounded-lg text-white hover:bg-white hover:bg-opacity-10 
                             <?php echo $current_page == 'system_settings.php' ? 'active' : ''; ?>">  
                            <i class="fas fa-cogs ml-2"></i> إعدادات النظام
                        </a>
                    </li>

                    <li>
                        <a  href="reports.php"class="flex items-center p-3 rounded-lg text-white hover:bg-white hover:bg-opacity-10
                                 <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>" >
                            <i class="fas fa-chart-bar ml-2"></i> التقارير
                        </a>
                    </li>

                    <li>
                        <a href="../logout.php" class="flex items-center p-3 rounded-lg text-white hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-sign-out-alt ml-2"></i> تسجيل الخروج
                        </a>
                    </li>



           
            </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 mr-64 p-8">
            <div class="container mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8 fade-in">
                    <h1 class="text-3xl font-bold text-gray-800">لوحة التحكم</h1>
                    <?php if ($maintenance_mode): ?>
                        <div class="maintenance-alert flex items-center px-4 py-2 rounded-lg text-yellow-800">
                            <i class="fas fa-wrench mr-2"></i> النظام في وضع الصيانة
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="card">

                    <div class="circle">
                        <svg width="150" height="150">
                        <circle class="bg" cx="75" cy="75" r="70" />
                        <circle class="progress" cx="75" cy="75" r="70" />
                        </svg>
                        <div class="percent" id="percent"> 
                            <div>
                                <h6 class="text-sm font-medium text-gray-600">مواعيد اليوم</h6>
                                <h2 class="text-2xl font-bold text-gray-800"><?php echo $today_stats['today_appointments']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="subtitle"> </div> <br>
                         <div class="mt-4 text-sm">
                            <span class="text-blue-500"><?php echo $today_stats['completed_appointments']; ?> مكتمل</span> |
                            <span class="text-yellow-600"><?php echo $today_stats['pending_appointments']; ?> معلق</span>
                        </div>
                    </div>

                        <div class="card">
                    <div class="circle">
                        <svg width="150" height="150">
                        <circle class="bg" cx="75" cy="75" r="70" />
                        <circle class="progress" cx="75" cy="75" r="70" />
                        </svg>
                        <div class="percent" id="percent"> 
                            <div>
                                <h6 class="text-sm font-medium text-gray-600">مرضى جدد</h6>
                                <h2 class="text-2xl font-bold text-gray-800"><?php echo $today_stats['new_patients']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <br>
                        <div class="mt-4">
                            <a href="manage_patients.php" class="text-blue-600 hover:underline text-sm"
                             style="text-decoration:none; margin-top: 10px">
                                عرض جميع المرضى <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>      

                         <div class="card">
                            <div class="circle">
                        <svg width="150" height="150">
                        <circle class="bg" cx="75" cy="75" r="70" />
                        <circle class="progress" cx="75" cy="75" r="70" />
                        </svg>
                        <div class="percent" id="percent">
                            <div>
                                <h6 class="text-sm font-medium text-gray-600">أطباء جدد</h6>
                                <h2 class="text-2xl font-bold text-gray-800"><?php echo $today_stats['new_doctors']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <br>
                        <div class="mt-4">
                            <a href="manage_doctors.php" class="text-blue-600 hover:underline text-sm">
                                عرض جميع الأطباء <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div> 

                        <div class="card">
                    <div class="circle">
                        <svg width="150" height="150">
                        <circle class="bg" cx="75" cy="75" r="70" />
                        <circle class="progress" cx="75" cy="75" r="70" />
                        </svg>
                        <div class="percent" id="percent">
                            <div>
                                <h6 class="text-sm font-medium text-gray-600">مواعيد قادمة</h6>
                                <h2 class="text-2xl font-bold text-gray-800"><?php echo $alerts['upcoming_appointments']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <br>
                        <div class="mt-4">
                            <a href="manage_appointments.php" class="text-blue-600 hover:underline text-sm">
                                عرض جميع المواعيد <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                 <!-- Recent Activities and Medical Records  -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Recent Activities -->
                    <div class="card bg-white rounded-lg shadow-lg">
                        <div class="p-6">
                            <h5 class="text-lg font-bold mb-4">النشاطات الأخيرة</h5>
                            <?php while($activity = $activities->fetch_assoc()): ?>
                                <div class="activity-item">
                                    <small class="text-gray-500"><?php echo date('Y/m/d H:i', strtotime($activity['date'])); ?></small>
                                    <p class="mb-0 text-gray-700"><?php echo htmlspecialchars($activity['description']); ?></p>
                                </div>
                            <?php endwhile; ?>
                        </div> 
                    </div>

                    <!-- Recent Medical Records -->
                    <div class="card bg-white rounded-lg shadow-lg">
                        <div class="p-6">
                            <h5 class="text-lg font-bold mb-4">السجلات الطبية الأخيرة</h5>

                            <div class="overflow-x-auto" style="box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);">
                                <table class="table w-full">
                                    <thead>
                                        <tr class="text-gray-600">
                                            <th>المريض</th>
                                            <th>الطبيب</th>
                                            <th>التاريخ</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($record = $records->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                                                <td>د. <?php echo htmlspecialchars($record['doctor_name']); ?></td>
                                                <td><?php echo date('Y/m/d', strtotime($record['created_at'])); ?></td>
                                                <td>
                                                    <a href="../view_patient.php?patient_id=<?php echo $record['patient_id']; ?>" 
                                                       class="text-blue-600 hover:text-blue-800">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Appointments -->
                <div class="card bg-white rounded-lg shadow-lg">
                    <div class="p-6">
                        <h5 class="text-lg font-bold mb-4">المواعيد القادمة</h5>
                        <div class="overflow-x-auto" style="box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);">
                            <table class="table w-full" style="height:8rem;">
                                <thead>
                                    <tr class="text-gray-600">
                                        <th>التاريخ</th>
                                        <th>المريض</th>
                                        <th>رقم الهاتف</th>
                                        <th>الطبيب</th>
                                        <th>التخصص</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($appointment = $upcoming->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('Y/m/d H:i', strtotime($appointment['appointment_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['patient_contact_number']); ?></td>
                                            <td>د. <?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['doctor_specialty']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $appointment['status'] == 'pending' ? 'yellow-500 text-yellow-800' : 
                                                         ($appointment['status'] == 'confirmed' ? 'green-500 text-green-800' : 
                                                         ($appointment['status'] == 'completed' ? 'blue-500 text-blue-800' : 'blue-500 text-white')); 
                                                ?>">
                                                    <?php echo $appointment['status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="edit_appointment.php?id=<?php echo $appointment['appointment_id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
