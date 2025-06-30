<?php
include 'db.php';
session_start();

// التحقق من تسجيل دخول الطبيب
if (!isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // حذف الأوقات القديمة للطبيب
    $delete_sql = "DELETE FROM doctor_working_hours WHERE doctor_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param('i', $doctor_id);
    $delete_stmt->execute();

    // إضافة الأوقات الجديدة
    $insert_sql = "INSERT INTO doctor_working_hours (doctor_id, day, start_time, end_time, is_working) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);

    foreach ($_POST['working_days'] as $day => $is_working) {
        $start_time = $_POST['start_time'][$day];
        $end_time = $_POST['end_time'][$day];
        $is_working = isset($_POST['working_days'][$day]) ? 1 : 0;

        $insert_stmt->bind_param('iissi', $doctor_id, $day, $start_time, $end_time, $is_working);
        $insert_stmt->execute();
    }

    // إضافة رسالة نجاح في الجلسة
    $_SESSION['success_message'] = 'تم تحديث أوقات العمل بنجاح';
    
    // إعادة التوجيه إلى صفحة الملف الشخصي
    header('Location: doctor_profile.php');
    exit;
} else {
    // إذا تم الوصول للصفحة بدون POST request
    header('Location: doctor_profile.php');
    exit;
}