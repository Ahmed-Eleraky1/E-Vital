<!-- <?php
include 'db.php'; // الاتصال بقاعدة البيانات

session_start(); // بدء الجلسة
if (!isset($_SESSION['doctor_id']) && !isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_GET['patient_id']; // الحصول على ID المريض من الرابط
$doctor_id = $_SESSION['doctor_id'];
// معالجة النموذج إذا تم إرساله
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['appointment_date'];
    $notes = $_POST['notes'];

    // إدخال البيانات في قاعدة البيانات
    $sql = "INSERT INTO appointments (patient_id, appointment_date,doctor_id , notes) 
            VALUES ('$patient_id', '$date',  '$doctor_id', '$notes')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: view_patient.php?patient_id=$patient_id");
        exit();
    } else {
        $error = "حدث خطأ أثناء إضافة الموعد: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة موعد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; // تضمين الهيدر ?>
    <div class="container mt-5">
    <a href="view_patient.php?patient_id=<?php echo $patient_id; ?>'" class="btn btn" style="float: left;color: #000000; background-color: #ffffff"> الرجوع <i class="fa-solid fa-chevron-left"></i></a>
        <h1>إضافة موعد</h1>
        <br>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="book_appointment.php">
            <div class="mb-3">
                <label for="appointment_date" class="form-label">تاريخ الموعد</label>
                <input type="datetime-local" class="form-control" id="appointment_date" name="appointment_date" required>
            </div>


            <div class="mb-3">
                <label for="notes" class="form-label">ملاحظات</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>

            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
            
            <button type="submit" class="btn btn-primary">حفظ الموعد</button>
        </form>
    </div>
</body>
</html> -->