<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

if (!isset($_GET['doctor_id'])) {
    header("Location: manage_doctors.php");
    exit();
}

$doctor_id = $_GET['doctor_id'];

// Get doctor details
$sql = "SELECT * FROM doctors WHERE doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    header("Location: manage_doctors.php");
    exit();
}

// Get appointments for the next 7 days
$sql = "SELECT 
    a.*,
    p.name as patient_name,
    p.contact_number as patient_contact_number
FROM appointments a
JOIN patients p ON a.patient_id = p.patient_id
WHERE a.doctor_id = ? 
AND a.appointment_date BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY)
ORDER BY a.appointment_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$appointments_result = $stmt->get_result();

// Organize appointments by date
$appointments_by_date = [];
while ($appointment = $appointments_result->fetch_assoc()) {
    $date = date('Y-m-d', strtotime($appointment['appointment_date']));
    if (!isset($appointments_by_date[$date])) {
        $appointments_by_date[$date] = [];
    }
    $appointments_by_date[$date][] = $appointment;
}

// Get appointment statistics
$sql = "SELECT 
    COUNT(*) as total_appointments,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_appointments,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_appointments,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_appointments
FROM appointments 
WHERE doctor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول مواعيد الطبيب - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <style>
        .schedule-card {
            transition: transform 0.2s;
        }
        .schedule-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .appointment-item {
            border-left: 4px solid;
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .appointment-item.pending { border-left-color: #ffc107; }
        .appointment-completed { border-left-color: #28a745; }
        .appointment-item.cancelled { border-left-color: #dc3545; }
        body {
            padding-right: 260px; /* يبعد المحتوى عن السايد بار */
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-0" 
    style=
    "font-size: 21px;
    font-weight: bold;
    margin-top: 16px;"
    >جدول مواعيد د. <?php echo htmlspecialchars($doctor['name']); ?></h1>
                <p class="text-muted" 
    style=
    "font-size: 21px;
    font-weight: 500;
    margin-top: 16px;"><?php echo htmlspecialchars($doctor['specialty']); ?></p>
            </div>
            <a href="manage_doctors.php" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> عودة للأطباء
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">إجمالي المواعيد</h5>
                        <h2><?php echo $stats['total_appointments']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">المواعيد المكتملة</h5>
                        <h2><?php echo $stats['completed_appointments']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">المواعيد المعلقة</h5>
                        <h2><?php echo $stats['pending_appointments']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">المواعيد الملغاة</h5>
                        <h2><?php echo $stats['cancelled_appointments']; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Cards -->
        <div class="row">
            <?php 
            for ($i = 0; $i < 7; $i++) {
                $date = date('Y-m-d', strtotime("+$i days"));
                $day_name = date('l', strtotime($date));
                $formatted_date = date('Y/m/d', strtotime($date));
            ?>
                <div class="col-md-6 mb-4">
                    <div class="card schedule-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <?php echo $day_name . ' - ' . $formatted_date; ?>
                                <span class="badge bg-primary float-start">
                                    <?php echo count($appointments_by_date[$date] ?? []); ?> مواعيد
                                </span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($appointments_by_date[$date])): ?>
                                <?php foreach ($appointments_by_date[$date] as $appointment): ?>
                                    <div class="appointment-item <?php echo $appointment['status']; ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">
                                                    <?php echo date('h:i A', strtotime($appointment['appointment_date'])); ?>
                                                    - <?php echo htmlspecialchars($appointment['patient_name']); ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone"></i> <?php echo htmlspecialchars($appointment['patient_contact_number']); ?>
                                                </small>
                                            </div>
                                            <div>
                                                <span class="badge bg-<?php 
                                                    echo $appointment['status'] == 'pending' ? 'warning' : 
                                                         ($appointment['status'] == 'completed' ? 'success' : 'danger'); 
                                                ?>">
                                                    <?php echo $appointment['status']; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <?php if ($appointment['notes']): ?>
                                            <small class="d-block mt-2">
                                                <strong>ملاحظات:</strong> <?php echo htmlspecialchars($appointment['notes']); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center my-3">لا توجد مواعيد لهذا اليوم</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>