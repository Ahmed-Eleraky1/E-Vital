<?php
include '../db.php'; // الاتصال بقاعدة البيانات

// Get all specialties
$sql_specialties = "SELECT DISTINCT specialty FROM doctors";
$specialties_result = $conn->query($sql_specialties);

// Get doctors with their statistics
$sql_doctors = "SELECT 
    d.*, 
    COUNT(DISTINCT p.patient_id) as total_patients,
    COUNT(DISTINCT a.appointment_id) as total_appointments
FROM doctors d
LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
LEFT JOIN patients p ON a.patient_id = p.patient_id
GROUP BY d.doctor_id
ORDER BY d.name ASC";

$doctors_result = $conn->query($sql_doctors);

// Handle search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$specialty = isset($_GET['specialty']) ? $_GET['specialty'] : '';

if (!empty($search) || !empty($specialty)) {
    $sql_doctors = "SELECT 
        d.*, 
        COUNT(DISTINCT p.patient_id) as total_patients,
        COUNT(DISTINCT a.appointment_id) as total_appointments
    FROM doctors d
    LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
    LEFT JOIN patients p ON a.patient_id = p.patient_id
    WHERE 1=1";
    
    if (!empty($search)) {
        $sql_doctors .= " AND (d.name LIKE '%$search%' OR d.email LIKE '%$search%' OR d.contact_number LIKE '%$search%')";
    }
    
    if (!empty($specialty)) {
        $sql_doctors .= " AND d.specialty = '$specialty'";
    }
    
    $sql_doctors .= " GROUP BY d.doctor_id ORDER BY d.name ASC";
    $doctors_result = $conn->query($sql_doctors);
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض الأطباء - نظام إدارة المرضى المزمنين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <style>
        .doctor-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .doctor-card .card-body {
            flex: 1;
        }
        .doctor-card .card-footer {
            border-top: 1px solid rgba(0,0,0,.125);
            padding: 0.75rem 1.25rem;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1>قائمة الأطباء</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="add_doctor.php" class="btn btn-primary">إضافة طبيب جديد</a>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="ابحث عن اسم، بريد إلكتروني، أو رقم هاتف" 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" name="specialty">
                                    <option value="">اختر التخصص</option>
                                    <?php while($row = $specialties_result->fetch_assoc()): ?>
                                        <option value="<?php echo $row['specialty']; ?>" 
                                                <?php echo ($specialty == $row['specialty']) ? 'selected' : ''; ?>>
                                            <?php echo $row['specialty']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">بحث</button>
                                <a href="view_doctors.php" class="btn btn-secondary">إعادة تعيين</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctors List -->
        <div class="row">
            <?php while($row = $doctors_result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card doctor-card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['name']; ?></h5>
                            <p class="card-text">
                                <strong>التخصص:</strong> <?php echo $row['specialty']; ?><br>
                                <strong>البريد الإلكتروني:</strong> <?php echo $row['email']; ?><br>
                                <strong>رقم الهاتف:</strong> <?php echo $row['contact_number']; ?>
                            </p>
                            <div class="stats">
                                <p><i class="fas fa-user-md"></i> <?php echo $row['total_patients']; ?> مريض</p>
                                <p><i class="fas fa-calendar-check"></i> <?php echo $row['total_appointments']; ?> موعد</p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group btn-group-sm w-100">
                                <a href="edit_doctor.php?doctor_id=<?php echo $row['doctor_id']; ?>" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                                <a href="delete_doctor.php?doctor_id=<?php echo $row['doctor_id']; ?>" 
                                   class="btn btn-outline-danger" 
                                   onclick="return confirm('هل أنت متأكد من حذف هذا الطبيب؟');">
                                    <i class="fas fa-trash"></i> حذف
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>