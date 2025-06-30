<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$record_id = $_GET['id'];
$patient_id = $_GET['patient_id'];
$doctor_id = $_SESSION['doctor_id'];

// التحقق من أن السجل الطبي ينتمي إلى مريض الدكتور
$check_sql = "SELECT m.* FROM medicalrecords m 
              JOIN patient_doctors pd ON m.patient_id = pd.patient_id 
              WHERE m.record_id = ? AND pd.doctor_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $record_id, $doctor_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: search_patient.php");
    exit();
}

$record = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $prescription = $_POST['prescription'];

    $update_sql = "UPDATE medicalrecords SET diagnosis = ?, treatment = ?, prescription = ? WHERE record_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $diagnosis, $treatment, $prescription, $record_id);
    
    if ($update_stmt->execute()) {
        header("Location: view_patient.php?patient_id=" . $patient_id);
        exit();
    } else {
        $error = "حدث خطأ أثناء تحديث السجل الطبي";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل السجل الطبي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h1>تعديل السجل الطبي</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="diagnosis" class="form-label">التشخيص</label>
                <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required><?php echo $record['diagnosis']; ?></textarea>
            </div>

            <div class="mb-3">
                <label for="treatment" class="form-label">العلاج</label>
                <textarea class="form-control" id="treatment" name="treatment" rows="3" required><?php echo $record['treatment']; ?></textarea>
            </div>

            <!-- <div class="mb-3">
                <label for="prescription" class="form-label">الوصفة</label>
                <textarea class="form-control" id="prescription" name="prescription" rows="3" required><?php echo $record['prescription']; ?></textarea>
            </div> -->

            <button type="submit" class="btn btn-primary">تحديث السجل</button>
            <a href="view_patient.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</body>
</html>