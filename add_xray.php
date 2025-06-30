<?php
// الاتصال بقاعدة البيانات
include 'db.php';
session_start(); // بدء جلسة العمل 

// التحقق من وجود patient_id في الرابط
if (isset($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id']; // الحصول على ID المريض من الرابط
} else {
    die("معرف المريض غير موجود!");
}
$doctor_id = $_SESSION['doctor_id'];  
// التحقق من إرسال البيانات عبر POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // استلام البيانات من النموذج
    $xray_type_id = $_POST['xray_type_id'];
    $test_date = $_POST['test_date'];
    $notes = $_POST['notes'];


    // استعلام SQL لإدخال البيانات في جدول `patient_xrays`
    $sql = "INSERT INTO patient_xrays (patient_id, xray_type_id, test_date, notes,doctor_id)
            VALUES ('$patient_id', '$xray_type_id', '$test_date', '$notes','$doctor_id')";

    // تنفيذ الاستعلام
    if ($conn->query($sql) === TRUE) {
        header("Location: view_patient.php?patient_id=$patient_id");
    } else {
        echo "خطأ: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة الأشعة للمريض</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; // تضمين شريط التنقل ?>
    <div class="container mt-5">
    <a href="view_patient.php?patient_id=<?php echo $patient_id; ?>'" class="btn btn" style="float: left;color: #000000; background-color: #ffffff"> الرجوع <i class="fa-solid fa-chevron-left"></i></a>
        <h1>إضافة الأشعة للمريض</h1>
        <br>

        
        <form action="add_xray.php?patient_id=<?php echo $patient_id; ?>" method="POST">
            <div class="mb-3">
                <label for="xray_type_id" class="form-label">نوع الأشعة</label>
                <select class="form-control" id="xray_type_id" name="xray_type_id" required>
                    <option value="">اختر نوع الأشعة</option>
                    <!-- أضف هنا الخيارات المأخوذة من جدول xraytypes -->
                    <?php
                    $sql_xrays = "SELECT * FROM xraytypes";
                    $xray_result = $conn->query($sql_xrays);
                    while($row = $xray_result->fetch_assoc()) {
                        echo "<option value='".$row['xray_type_id']."'>".$row['xray_name']."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="test_date" class="form-label">تاريخ الأشعة</label>
                <input type="date" class="form-control" id="test_date" name="test_date" required>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">الملاحظات</label>
                <textarea class="form-control" id="notes" name="notes"></textarea>
            </div>
            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
            <button type="submit" class="btn btn-primary">إضافة الأشعة</button>
        </form>
    </div>
</body>
</html>
