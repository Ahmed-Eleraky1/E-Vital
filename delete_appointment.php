<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['patient_id'])) {
    $appointment_id = $_GET['id'];
    $patient_id = $_GET['patient_id'];
    $doctor_id = $_SESSION['doctor_id'];

    // التحقق من أن الموعد ينتمي إلى مريض الدكتور
    $check_sql = "SELECT a.* FROM appointments a 
                  JOIN patient_doctors pd ON a.patient_id = pd.patient_id 
                  WHERE a.appointment_id = ? AND pd.doctor_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $appointment_id, $doctor_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_sql = "DELETE FROM appointments WHERE appointment_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $appointment_id);
        
        if ($delete_stmt->execute()) {
            header("Location: view_patient.php?patient_id=" . $patient_id);
        } else {
            echo "حدث خطأ أثناء حذف الموعد";
        }
    } else {
        echo "غير مصرح لك بحذف هذا الموعد";
    }
} else {
    echo "معرف الموعد غير صحيح";
}
?>