<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$prescription_id = $_GET['id'];
$patient_id = $_GET['patient_id'];
$doctor_id = $_SESSION['doctor_id'];

// التحقق من أن الوصفة تنتمي إلى مريض الدكتور
$check_sql = "SELECT pr.* FROM prescriptions pr 
              JOIN patient_doctors pd ON pr.patient_id = pd.patient_id 
              WHERE pr.prescription_id = ? AND pd.doctor_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $prescription_id, $doctor_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: search_patient.php");
    exit();
}

$prescription = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $medicine_name = $_POST['medicine_name'];
    $dosage = $_POST['dosage'];
    $instructions = $_POST['instructions'];

    $update_sql = "UPDATE prescriptions SET medicine_name = ?, dosage = ?, instructions = ? WHERE prescription_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $medicine_name, $dosage, $instructions, $prescription_id);
    
    if ($update_stmt->execute()) {
        header("Location: view_patient.php?patient_id=" . $patient_id);
        exit();
    } else {
        $error = "حدث خطأ أثناء تحديث الوصفة الطبية";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الوصفة الطبية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h1>تعديل الوصفة الطبية</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="medicine_name" class="form-label">اسم الدواء</label>
                <input type="text" class="form-control" id="medicine_name" name="medicine_name" value="<?php echo $prescription['medicine_name']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="dosage" class="form-label">الجرعة</label>
                <input type="text" class="form-control" id="dosage" name="dosage" value="<?php echo $prescription['dosage']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="instructions" class="form-label">التعليمات</label>
                <textarea class="form-control" id="instructions" name="instructions" rows="3" required><?php echo $prescription['instructions']; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">تحديث الوصفة</button>
            <a href="view_patient.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</body>
</html>