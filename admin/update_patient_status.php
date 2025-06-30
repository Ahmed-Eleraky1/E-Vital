<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

// Check if patient_id and status are provided
if (!isset($_GET['patient_id']) || !isset($_GET['status'])) {
    $_SESSION['error_message'] = "بيانات غير كافية لتحديث حالة المريض";
    header("Location: manage_patients.php");
    exit();
}

$patient_id = $_GET['patient_id'];
$status = $_GET['status'];

// Validate status value (only allow 'active' or 'inactive')
if ($status !== 'active' && $status !== 'inactive') {
    $_SESSION['error_message'] = "قيمة الحالة غير صالحة";
    header("Location: view_patient.php?patient_id=$patient_id");
    exit();
}

// Update patient status
$sql = "UPDATE patients SET status = ? WHERE patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $patient_id);

if ($stmt->execute()) {
    // Success
    $status_text = ($status === 'active') ? "تفعيل" : "تعطيل";
    $_SESSION['success_message'] = "تم $status_text حساب المريض بنجاح";
} else {
    // Error
    $_SESSION['error_message'] = "حدث خطأ أثناء تحديث حالة المريض: " . $conn->error;
}

// Redirect back to patient view
header("Location: view_patient.php?patient_id=$patient_id");
exit();
