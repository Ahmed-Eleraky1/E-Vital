<?php
include 'db.php'; // الاتصال بقاعدة البيانات
session_start(); // بدء الجلسة
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}


$patient_id = $_GET['patient_id']; // الحصول على ID المريض من الرابط
$doctor_id = $_SESSION['doctor_id'];    
// جلب أنواع الاختبارات المخبرية
$sql_test_types = "SELECT * FROM labtesttypes";
$test_types_result = $conn->query($sql_test_types);

// معالجة النموذج إذا تم إرساله
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test_type_id = mysqli_real_escape_string($conn, $_POST['test_type']);
    $test_date = mysqli_real_escape_string($conn, $_POST['test_date']);
    $result = mysqli_real_escape_string($conn, $_POST['result']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    // إدخال البيانات في قاعدة البيانات
    $sql = "INSERT INTO labtests (patient_id, doctor_id, test_type_id, test_date, result, notes) 
            VALUES ('$patient_id', '$doctor_id', '$test_type_id', '$test_date', '$result', '$notes')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: view_patient.php?patient_id=$patient_id");
        exit();
    } else {
        $error = "حدث خطأ أثناء إضافة الاختبار المخبري: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اضافة تحليل</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; // تضمين الهيدر ?>
    <div class="container mt-5">
    <a href="view_patient.php?patient_id=<?php echo $patient_id; ?>'" class="btn btn" style="float: left;color: #000000; background-color: #ffffff"> الرجوع <i class="fa-solid fa-chevron-left"></i></a>
        <h1>إضافة تحليل</h1>
        <br>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="test_type" class="form-label">نوع التحليل</label>
                <select class="form-control" id="test_type" name="test_type" required>
                    <option value="">اختر نوع التحليل</option>
                    <?php while($row = $test_types_result->fetch_assoc()): ?>
                        <option value="<?php echo $row['test_type_id'];  ?>">
                            <?php echo $row['test_name']; ?>
                            <?php if ($row['normal_range']): ?>
                                (مدى طبيعي: <?php echo $row['normal_range']; ?> <?php echo $row['unit']; ?>)
                            <?php endif; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="test_date" class="form-label">تاريخ التخليل</label>
                <input type="date" class="form-control" id="test_date" name="test_date" required>
            </div>

            <div class="mb-3">
                <label for="result" class="form-label">النتيجة</label>
                <input type="text" class="form-control" id="result" name="result" required>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">ملاحظات</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>

            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
            
            <button type="submit" class="btn btn-primary mb-3">حفظ التحليل</button>

        </form>
    </div>
</body>
</html>