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


// Handle patient status updates
if (isset($_POST['update_status'])) {
    $patient_id = $_POST['patient_id'];
    $new_status = $_POST['status'];
    
    $sql = "UPDATE patients SET status = ? WHERE patient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $patient_id);
    $stmt->execute();
}

// Get all patients with their statistics
$sql = "SELECT 
    p.*,
    COUNT(DISTINCT a.appointment_id) as total_appointments,
    COUNT(DISTINCT pr.prescription_id) as total_prescriptions,
    COUNT(DISTINCT l.test_id) as total_labtests,
    COUNT(DISTINCT x.patient_xray_id) as total_xrays
FROM patients p
LEFT JOIN appointments a ON p.patient_id = a.patient_id
LEFT JOIN prescriptions pr ON p.patient_id = pr.patient_id
LEFT JOIN labtests l ON p.patient_id = l.patient_id
LEFT JOIN patient_xrays x ON p.patient_id = x.patient_id
GROUP BY p.patient_id
ORDER BY p.name ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المرضى - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->
    <style>
        .patient-card {
            transition: transform 0.2s;
            border-radius: 20px  
        }
        .patient-card:hover {
            transition: transform 0.2s;
    
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 15px 0;
        }
        .stats p {
            margin: 0;
            text-align: center;
        }
        .stats i {
            color: #3b82f6;
            margin-right: 5px;
        }
        /* .card-footer button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #f5f5f5;
            color: #333 !important;
            font-size: 16px;
            font-weight: 500;
        } */
    </style>
</head>
<body>
    <?php  include 'admin_navbar.php'; ?>
    

<div class="mr-64 p-6">
        <div class="row">
        <div class="d-flex justify-content-between align-items-center mb-4 ">
            <h1 class="text-3xl font-bold text-gray-800">إدارة المرضى</h1>
        </div>
            <?php while($patient = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card patient-card">
                        <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($patient['name'] ?? ''); ?></h5>
                        <p class="card-text">
                            <strong>البريد الإلكتروني:</strong> <?php echo htmlspecialchars($patient['email'] ?? ''); ?><br>
                            <strong>رقم الهاتف:</strong> <?php echo htmlspecialchars($patient['contact_number'] ?? ''); ?><br>
                            <strong>العمر:</strong> <?php echo htmlspecialchars($patient['date_of_birth'] ?? ''); ?> سنة<br>
                            <strong>الحالة:</strong> 
                            <span class="badge bg-<?php echo ($patient['status'] ?? '') == 'active' ? 'blue-500' : 'warning'; ?>">
                                <?php echo ($patient['status'] ?? '') == 'active' ? 'نشط' : 'غير نشط'; ?>
                            </span>
                        </p>

                            <div class="stats">
                                <p><i class="fas fa-calendar-check"></i> <?php echo $patient['total_appointments']; ?> موعد</p>
                                <p><i class="fas fa-prescription"></i> <?php echo $patient['total_prescriptions']; ?> وصفة</p>
                                <p><i class="fas fa-flask"></i> <?php echo $patient['total_labtests']; ?> فحص</p>
                                <p><i class="fas fa-x-ray"></i> <?php echo $patient['total_xrays']; ?> أشعة</p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="patient_id" value="<?php echo $patient['patient_id']; ?>">
                                <input type="hidden" name="status" 
                                       value="<?php echo $patient['status'] == 'active' ? 'inactive' : 'active'; ?>" >
                                <button type="submit" name="update_status" class="btn btn-warning btn-sm">
                                    <i class="fas fa-sync"></i>
                                    تغيير الحالة
                                </button>
                            </form>
                            <a href="../view_patient.php?patient_id=<?php echo $patient['patient_id']; ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i>
                                عرض التفاصيل
                            </a>
                            <button type="button" class="btn btn-danger btn-sm"  
                                    onclick="deletePatient(<?php echo $patient['patient_id']; ?>)" style="padding: 4px 40px;">
                                <i class="fas fa-trash"></i>
                                حذف
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function deletePatient(patientId) {
        if (confirm('هل أنت متأكد من حذف هذا المريض؟')) {
            window.location.href = `delete_patient.php?patient_id=${patientId}`;
        }
    }
    </script>
</body>
</html>