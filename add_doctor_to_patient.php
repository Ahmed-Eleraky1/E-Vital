<?php
include 'db.php';
session_start();

if (!isset($_SESSION['patient_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_SESSION['patient_id'];
    $doctor_id = $_POST['doctor_id'];

    // التحقق من أن هذا الطبيب غير مسجل مع المريض من قبل
    $check_sql = "SELECT * FROM patient_doctors WHERE patient_id = ? AND doctor_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $patient_id, $doctor_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "أنت مسجل بالفعل مع هذا الطبيب";
    } else {
        // إضافة العلاقة بين المريض والطبيب
        $sql = "INSERT INTO patient_doctors (patient_id, doctor_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $patient_id, $doctor_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "تم إضافة الطبيب بنجاح";
        } else {
            $_SESSION['error'] = "حدث خطأ أثناء إضافة الطبيب";
        }
    }
    
    header('Location: profile.php');
    exit;
}