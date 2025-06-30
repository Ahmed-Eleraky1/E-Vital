<?php
session_start();
include 'db.php';

// التحقق من تسجيل دخول الطبيب
if (!isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit();
}

// التحقق من إرسال نموذج إضافة المريض
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['patient_id'])) {
    $doctor_id = $_SESSION['doctor_id'];
    $patient_id = $_POST['patient_id'];

    // التحقق من عدم وجود علاقة سابقة بين الطبيب والمريض
    $check_sql = "SELECT * FROM patient_doctors 
                 WHERE doctor_id = ? AND patient_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('ii', $doctor_id, $patient_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        // إضافة العلاقة بين الطبيب والمريض
        $insert_sql = "INSERT INTO patient_doctors (doctor_id, patient_id, registration_date) 
                      VALUES (?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param('ii', $doctor_id, $patient_id);

        if ($insert_stmt->execute()) {
            $_SESSION['success_message'] = 'تمت إضافة المريض بنجاح';
        } else {
            $_SESSION['error_message'] = 'حدث خطأ أثناء إضافة المريض';
        }

        $insert_stmt->close();
    } else {
        $_SESSION['error_message'] = 'المريض مسجل بالفعل مع هذا الطبيب';
    }

    $check_stmt->close();
} else {
    $_SESSION['error_message'] = 'بيانات غير صحيحة';
}

// إعادة التوجيه إلى صفحة الملف الشخصي للطبيب
header('Location: doctor_profile.php');
exit();