<?php
include 'db.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// التحقق من وجود معرف الطبيب
if (!isset($_SESSION['doctor_id'])) {
    $_SESSION['error'] = "عذراً، هذه الصفحة متاحة فقط للأطباء";
    header("Location: index.php");
    exit;
}

$patient_id = $_GET['patient_id'];
$doctor_id = $_SESSION['doctor_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $prescription = $_POST['prescription'];

    // تعديل الاستعلام لإضافة معرف الطبيب
    $sql = "INSERT INTO medicalrecords (patient_id, doctor_id, diagnosis, treatment, prescription) 
            VALUES (?, ?, ?, ?, ?)";
    
    // استخدام Prepared Statement لمنع SQL Injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $patient_id, $doctor_id, $diagnosis, $treatment, $prescription);
    
    if ($stmt->execute()) {
        header("Location: view_patient.php?patient_id=$patient_id");
        exit();
    } else {
        $error = "حدث خطأ أثناء إضافة السجل الطبي: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة سجل طبي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; // تضمين الهيدر ?>

    <div class="container mt-5">
    <a href="view_patient.php?patient_id=<?php echo $patient_id; ?>'" class="btn btn" style="float: left;color: #000000; background-color: #ffffff"> الرجوع <i class="fa-solid fa-chevron-left"></i></a>
        <h1>إضافة سجل طبي</h1>
        <br>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="diagnosis" class="form-label">التشخيص</label>
                <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="treatment" class="form-label">العلاج</label>
                <textarea class="form-control" id="treatment" name="treatment" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="prescription" class="form-label">الوصفة</label>
                <textarea class="form-control" id="prescription" name="prescription" rows="3" required></textarea>
            </div>
            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id;?>">


            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
            
            <button type="submit" class="btn btn-primary mb-3">حفظ السجل</button>

        </form>
    </div>
</body>
</html>
