<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['patient_id'])) {
    $xray_id = $_GET['id'];
    $patient_id = $_GET['patient_id'];
    $doctor_id = $_SESSION['doctor_id'];

    // التحقق من أن الأشعة تنتمي إلى مريض الدكتور
    $check_sql = "SELECT x.* FROM patient_xrays x 
                  JOIN patient_doctors pd ON x.patient_id = pd.patient_id 
                  WHERE x.patient_xray_id = ? AND pd.doctor_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $xray_id, $doctor_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_sql = "DELETE FROM patient_xrays WHERE patient_xray_id  = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $xray_id);
        
        if ($delete_stmt->execute()) {
            header("Location: view_patient.php?patient_id=" . $patient_id);
        } else {
            echo "حدث خطأ أثناء حذف الأشعة";
        }
    } else {
        echo "غير مصرح لك بحذف هذه الأشعة";
    }
} else {
    echo "معرف الأشعة غير صحيح";
}
?>