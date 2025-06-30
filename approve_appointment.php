<?php
session_start();
include 'db.php';

// التحقق من تسجيل دخول الطبيب
if (!isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['appointment_id']) && isset($_POST['action'])) {
    $doctor_id = intval($_SESSION['doctor_id']);
    $appointment_id = intval($_POST['appointment_id']);
    $action = $_POST['action']; // 'approve' or 'reject'

    // التحقق من أن الموعد ينتمي للطبيب
    $check_sql = "SELECT a.*, p.name as patient_name 
                  FROM appointments a 
                  JOIN patients p ON a.patient_id = p.patient_id 
                  WHERE a.appointment_id = $appointment_id 
                  AND a.doctor_id = $doctor_id 
                  AND a.status = 'Scheduled'";
    
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        $appointment = $result->fetch_assoc();
        $patient_id = $appointment['patient_id'];
        $patient_name = $appointment['patient_name'];
        $new_status = ($action == 'Completed') ? 'Completed' : 'Cancelled';

        // تحديث حالة الموعد
        $update_sql = "UPDATE appointments 
                       SET status = '$new_status' 
                       WHERE appointment_id = $appointment_id";

        if ($conn->query($update_sql)) {
            // إضافة إشعار للمريض
            $message = ($action == 'Completed') 
                ? "تمت الموافقة على موعدك مع الدكتور" 
                : "تم رفض موعدك مع الدكتور";

            $notification_sql = "INSERT INTO notifications (user_id, user_type, message, related_id, type, created_at) 
                                VALUES ($patient_id, 'patient', '$message', $appointment_id, 'appointment_$new_status', NOW())";
            $conn->query($notification_sql);

            $_SESSION['success'] = ($action == 'Completed') 
                ? "تمت الموافقة على الموعد بنجاح" 
                : "تم رفض الموعد بنجاح";
        } else {
            $_SESSION['error'] = "حدث خطأ أثناء تحديث حالة الموعد";
        }
    } else {
        $_SESSION['error'] = "لم يتم العثور على الموعد أو ليس لديك صلاحية تحديثه";
    }

    $conn->close();
    header('Location: doctor_profile.php');
    exit;
} else {
    header('Location: doctor_profile.php');
    exit;
}