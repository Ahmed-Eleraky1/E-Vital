<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['patient_id'])) {
    $prescription_id = $_GET['id'];
    $patient_id = $_GET['patient_id'];
    $doctor_id = $_SESSION['doctor_id'];

    // التحقق من أن الوصفة تنتمي إلى مريض الدكتور
    $check_sql = "SELECT pr.* FROM prescriptions pr 
                  JOIN patient_doctors pd ON pr.patient_id = pd.patient_id 
                  WHERE pr.prescription_id = ? AND pd.doctor_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $prescription_id, $doctor_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_sql = "DELETE FROM prescriptions WHERE prescription_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $prescription_id);
        
        if ($delete_stmt->execute()) {
            header("Location: view_patient.php?patient_id=" . $patient_id);
        } else {
            echo "حدث خطأ أثناء حذف الوصفة";
        }
    } else {
        echo "غير مصرح لك بحذف هذه الوصفة";
    }
} else {
    echo "معرف الوصفة غير صحيح";
}
?>