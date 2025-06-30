<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];
    
    // Get appointment details first for the success message
    $sql = "SELECT p.name as patient_name, d.name as doctor_name, a.appointment_date 
            FROM appointments a
            JOIN patients p ON a.patient_id = p.patient_id
            JOIN doctors d ON a.doctor_id = d.doctor_id
            WHERE a.appointment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();
    
    // Delete the appointment
    $sql = "DELETE FROM appointments WHERE appointment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = sprintf(
            "تم حذف موعد المريض %s مع الدكتور %s بتاريخ %s بنجاح",
            $appointment['patient_name'],
            $appointment['doctor_name'],
            date('Y-m-d H:i', strtotime($appointment['appointment_date']))
        );
    } else {
        $_SESSION['error_message'] = "حدث خطأ أثناء حذف الموعد";
    }
}

header("Location: manage_appointments.php");
exit();