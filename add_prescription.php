<?php
include 'db.php'; // الاتصال بقاعدة البيانات
session_start(); // بدء جلسة العمل
$patient_id = $_GET['patient_id']; // الحصول على ID المريض من الرابط
$doctor_id = $_SESSION['doctor_id'];    
// معالجة النموذج إذا تم إرساله
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $medicine_name = $_POST['medicine_name'];
    $dosage = $_POST['dosage'];
    $instructions = $_POST['instructions'];

    // إدخال البيانات في قاعدة البيانات
    $sql = "INSERT INTO prescriptions (patient_id, doctor_id, medicine_name, dosage, instructions) 
            VALUES ('$patient_id', '$doctor_id', '$medicine_name', '$dosage', '$instructions')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: view_patient.php?patient_id=$patient_id");
        exit();
    } else {
        $error = "حدث خطأ أثناء إضافة الوصفة: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة وصفة طبية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; // تضمين شريط التنقل ?>
    <div class="container mt-5">
    <a href="view_patient.php?patient_id=<?php echo $patient_id; ?>'" class="btn btn" style="float: left;color: #000000; background-color: #ffffff"> الرجوع <i class="fa-solid fa-chevron-left"></i></a>
        <h1>إضافة وصفة طبية</h1>
        <br>

        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="medicine_name" class="form-label">اسم الدواء</label>
                <input type="text" class="form-control" id="medicine_name" name="medicine_name" required>
            </div>

            <div class="mb-3">
                <label for="dosage" class="form-label">الجرعة</label>
                <input type="text" class="form-control" id="dosage" name="dosage" required>
            </div>

            <div class="mb-3">
                <label for="instructions" class="form-label">التعليمات</label>
                <textarea class="form-control" id="instructions" name="instructions" rows="3" required></textarea>
            </div>

            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
            
            <button type="submit" class="btn btn-primary">حفظ الوصفة</button>

        </form>
    </div>
</body>
</html>