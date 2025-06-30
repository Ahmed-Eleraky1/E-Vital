<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$appointment_id = $_GET['id'];
$patient_id = $_GET['patient_id'];
$doctor_id = $_SESSION['doctor_id'];

// التحقق من أن الموعد ينتمي إلى مريض الدكتور
$check_sql = "SELECT a.* FROM appointments a 
              JOIN patient_doctors pd ON a.patient_id = pd.patient_id 
              WHERE a.appointment_id = ? AND pd.doctor_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $appointment_id, $doctor_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: search_patient.php");
    exit();
}

$appointment = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_date = $_POST['appointment_date'];
    $notes = $_POST['notes'];
    $status = $_POST['status'];

    $update_sql = "UPDATE appointments SET appointment_date = ?, notes = ?, status = ? WHERE appointment_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $appointment_date, $notes, $status, $appointment_id);
    
    if ($update_stmt->execute()) {
        header("Location: view_patient.php?patient_id=" . $patient_id);
        exit();
    } else {
        $error = "حدث خطأ أثناء تحديث الموعد";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الموعد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h1>تعديل الموعد</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="appointment_date" class="form-label">تاريخ ووقت الموعد</label>
                <input type="datetime-local" class="form-control" id="appointment_date" name="appointment_date" 
                       value="<?php echo date('Y-m-d\TH:i', strtotime($appointment['appointment_date'])); ?>" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">حالة الموعد</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="Scheduled" <?php echo ($appointment['status'] == 'Scheduled') ? 'selected' : ''; ?>>مجدول</option>
                    <option value="Completed" <?php echo ($appointment['status'] == 'Completed') ? 'selected' : ''; ?>>مكتمل</option>
                    <option value="Cancelled" <?php echo ($appointment['status'] == 'Cancelled') ? 'selected' : ''; ?>>ملغى</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">ملاحظات</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $appointment['notes']; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">تحديث الموعد</button>
            <a href="view_patient.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</body>
</html>