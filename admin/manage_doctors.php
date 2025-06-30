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

// Handle doctor status updates
if (isset($_POST['update_status'])) {
    $doctor_id = $_POST['doctor_id'];
    $new_status = $_POST['status'];

    $sql = "UPDATE doctors SET status = ? WHERE doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $doctor_id);
    $stmt->execute();
}

// Filters
$specialty_filter = $_GET['specialty'] ?? '';
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql_specialties = "SELECT DISTINCT specialty FROM doctors ORDER BY specialty";
$specialties_result = $conn->query($sql_specialties);

$sql = "SELECT 
    d.*, 
    COUNT(DISTINCT p.patient_id) AS total_patients,
    COUNT(DISTINCT a.appointment_id) AS total_appointments,
    COUNT(DISTINCT CASE WHEN a.status = 'completed' THEN a.appointment_id END) AS completed_appointments
FROM doctors d
LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
LEFT JOIN patient_doctors pd ON d.doctor_id = pd.doctor_id
LEFT JOIN patients p ON pd.patient_id = p.patient_id
WHERE 1=1";

if ($specialty_filter) $sql .= " AND d.specialty = '" . $conn->real_escape_string($specialty_filter) . "'";
if ($status_filter) $sql .= " AND d.status = '" . $conn->real_escape_string($status_filter) . "'";
if ($search) {
    $search_escaped = $conn->real_escape_string($search);
    $sql .= " AND (d.name LIKE '%$search_escaped%' OR d.email LIKE '%$search_escaped%' OR d.contact_number LIKE '%$search_escaped%')";
}

$sql .= " GROUP BY d.doctor_id ORDER BY d.name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الأطباء - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <style>
        .doctor-card {
            transition: all 0.3s ease;
            height: 100%;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 15px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-item i {
            font-size: 1.5em;
            margin-bottom: 5px;
            color: #3b82f6;
        }
        .card-columns-fixed {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100%, 1fr));
        }
        @media (min-width: 768px) {
            .card-columns-fixed {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>

    <div class="container mt-5" style="margin-right: 16rem;padding: 0 35px">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">إدارة الأطباء</h1>
            <a href="add_doctor.php" class="btn btn-success">
                <i class="fas fa-user-md"></i> إضافة طبيب جديد
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">التخصص</label>
                        <select name="specialty" class="form-select">
                            <option value="">كل التخصصات</option>
                            <?php while($specialty = $specialties_result->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($specialty['specialty']) ?>" <?= $specialty_filter == $specialty['specialty'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($specialty['specialty']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">كل الحالات</option>
                            <option value="active" <?= $status_filter == 'active' ? 'selected' : '' ?>>نشط</option>
                            <option value="inactive" <?= $status_filter == 'inactive' ? 'selected' : '' ?>>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="بحث بالاسم، البريد الإلكتروني، أو رقم الهاتف" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                </form>
            </div>
        </div>            
        <div class="card-columns-fixed gap-4">
            <?php while($doctor = $result->fetch_assoc()): ?>
                <div class="card doctor-card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">د. <?= htmlspecialchars($doctor['name']) ?></h5>
                        <span class="badge bg-<?= $doctor['status'] == 'active' ? 'success' : ($doctor['status'] == 'pending' ? 'warning' : 'secondary') ?>">
                            <?= $doctor['status'] == 'active' ? 'نشط' : ($doctor['status'] == 'pending' ? 'معلق' : 'غير نشط') ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <p><strong>التخصص:</strong> <?= htmlspecialchars($doctor['specialty']) ?></p>
                        <p><strong>البريد الإلكتروني:</strong> <?= htmlspecialchars($doctor['email']) ?></p>
                        <p><strong>رقم الهاتف:</strong> <?= htmlspecialchars($doctor['contact_number']) ?></p>

                        <div class="stats">
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <div><?= $doctor['total_patients'] ?></div>
                                <small>المرضى</small>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-calendar-check"></i>
                                <div><?= $doctor['total_appointments'] ?></div>
                                <small>المواعيد</small>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-check-circle"></i>
                                <div><?= $doctor['completed_appointments'] ?></div>
                                <small>مكتملة</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                        <form method="POST" class="d-flex align-items-center">
                            <input type="hidden" name="doctor_id" value="<?= $doctor['doctor_id'] ?>">
                            <select name="status" class="form-select form-select-sm me-2" style="margin-left: 33px;">
                                <option value="active" <?= $doctor['status'] == 'active' ? 'selected' : '' ?>>نشط</option>
                                <option value="inactive" <?= $doctor['status'] == 'inactive' ? 'selected' : '' ?>>غير نشط</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary btn-sm"
                        style="padding: 4px 3rem;" >
                            
                                <i class="fas fa-save"></i>
                            </button>
                        </form>
                        <a href="view_doctor_schedule.php?doctor_id=<?= $doctor['doctor_id'] ?>" class="btn btn-info btn-sm"
                        style="padding: 4px 2.4rem;" >
                            <i class="fas fa-calendar-alt"></i> جدول
                        </a>
                        <button class="btn btn-danger btn-sm" onclick="deleteDoctor(<?= $doctor['doctor_id'] ?>)"
                        style="padding: 4px 2.2rem;" >
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteDoctor(doctorId) {
            if (confirm('هل أنت متأكد من حذف هذا الطبيب؟')) {
                window.location.href = `delete_doctor.php?doctor_id=${doctorId}`;
            }
        }
    </script>
</body>
</html>
