<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['patient_id'])) {
    $record_id = $_GET['id'];
    $patient_id = $_GET['patient_id'];
    $doctor_id = $_SESSION['doctor_id'];

    // التحقق من أن السجل الطبي ينتمي إلى مريض الدكتور
    $check_sql = "SELECT m.* FROM medicalrecords m 
                  JOIN patient_doctors pd ON m.patient_id = pd.patient_id 
                  WHERE m.record_id = ? AND pd.doctor_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $record_id, $doctor_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_sql = "DELETE FROM medicalrecords WHERE record_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $record_id);
        
        if ($delete_stmt->execute()) {
            header("Location: view_patient.php?patient_id=" . $patient_id);
        } else {
            echo "حدث خطأ أثناء حذف السجل";
        }
    } else {
        echo "غير مصرح لك بحذف هذا السجل";
    }
} else {
    echo "معرف السجل غير صحيح";
}
?>