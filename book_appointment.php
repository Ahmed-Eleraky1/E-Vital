<?php
session_start();
include 'db.php';

// التحقق من تسجيل دخول المريض
if (!isset($_SESSION['patient_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = intval($_SESSION['patient_id']);
    $doctor_id = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : 0;
    $appointment_date_raw = $_POST['appointment_date'] ?? '';
    $notes_raw = $_POST['notes'] ?? '';

    if (!empty($appointment_date_raw)) {
        $appointment_date = mysqli_real_escape_string($conn, $appointment_date_raw);
        $notes = mysqli_real_escape_string($conn, $notes_raw);

        // تحويل التاريخ إلى كائن DateTime
        $appointment_datetime = new DateTime($appointment_date);
        $appointment_time = $appointment_datetime->format('H:i:s');
        $appointment_date_only = $appointment_datetime->format('Y-m-d');

        // التحقق من المواعيد المحجوزة في نفس اليوم
        $check_availability = "SELECT appointment_date 
                             FROM appointments 
                             WHERE doctor_id = $doctor_id 
                             AND DATE(appointment_date) = '$appointment_date_only'
                             AND status != 'cancelled'
                             ORDER BY appointment_date";
        
        $result = $conn->query($check_availability);
        $is_available = true;

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $booked_time = new DateTime($row['appointment_date']);
                $time_diff = abs($appointment_datetime->getTimestamp() - $booked_time->getTimestamp()) / 60;

                if ($time_diff < 30) {
                    $is_available = false;
                    break;
                }
            }
        }

        if (!$is_available) {
            $_SESSION['error'] = "عذراً، هذا الموعد غير متاح. الرجاء اختيار موعد آخر.";
            header('Location: profile.php');
            exit;
        }

        // إدخال الموعد في قاعدة البيانات
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, notes) 
                VALUES ($patient_id, $doctor_id, '$appointment_date', 'Scheduled', '$notes')";

        if ($conn->query($sql)) {
            $appointment_id = $conn->insert_id;

            // إشعار المريض
            $notification_sql = "INSERT INTO notifications (user_id, user_type, message, related_id, type, created_at) 
                                VALUES ($patient_id, 'patient', 'تم إرسال طلب حجز موعدك وبانتظار موافقة الطبيب', $appointment_id, 'appointment_request', NOW())";
            $conn->query($notification_sql);

            // إشعار الطبيب
            $notification_sql = "INSERT INTO notifications (user_id, user_type, message, related_id, type, created_at) 
                                VALUES ($doctor_id, 'doctor', 'لديك طلب حجز موعد جديد يحتاج إلى موافقتك', $appointment_id, 'appointment_request', NOW())";
            $conn->query($notification_sql);

            $_SESSION['success'] = "تم إرسال طلب حجز الموعد بنجاح وبانتظار موافقة الطبيب!";
        } else {
            $_SESSION['error'] = "حدث خطأ أثناء حجز الموعد. الرجاء المحاولة مرة أخرى.";
        }

        $conn->close();
        header('Location: profile.php');
        exit;

    } else {
        $_SESSION['error'] = "لا توجد مواعيد متاحه";
        header('Location: profile.php');
        exit;
    }

} else {
    // إذا تم الوصول للصفحة بدون POST
    header('Location: profile.php');
    exit;
}
