<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['patient_id'])) {
    $test_id = $_GET['id'];
    $patient_id = $_GET['patient_id'];
    $doctor_id = $_SESSION['doctor_id'];

    // التحقق من أن التحليل ينتمي إلى مريض الدكتور
    $check_sql = "SELECT l.* FROM labtests l 
                  JOIN patient_doctors pd ON l.patient_id = pd.patient_id 
                  WHERE l.test_id = ? AND pd.doctor_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $test_id, $doctor_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_sql = "DELETE FROM labtests WHERE test_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $test_id);
        
        if ($delete_stmt->execute()) {
            header("Location: view_patient.php?patient_id=" . $patient_id);
        } else {
            echo "حدث خطأ أثناء حذف الاختبار";
        }
    } else {
        echo "غير مصرح لك بحذف هذا الاختبار";
    }
} else {
    echo "معرف الاختبار غير صحيح";
}
?>