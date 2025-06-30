<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

if (isset($_GET['doctor_id'])) {
    $doctor_id = $_GET['doctor_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Get doctor details first for the success message
        $sql = "SELECT name FROM doctors WHERE doctor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        
        // Delete related records first
        $tables = [
            'appointments',
            'prescriptions',
            'patient_doctors'
        ];
        
        foreach ($tables as $table) {
            $sql = "DELETE FROM $table WHERE doctor_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $doctor_id);
            $stmt->execute();
        }
        
        // Finally, delete the doctor
        $sql = "DELETE FROM doctors WHERE doctor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctor_id);
        
        if ($stmt->execute()) {
            // Commit transaction
            $conn->commit();
            $_SESSION['success_message'] = sprintf(
                "تم حذف الطبيب %s وجميع سجلاته المرتبطة بنجاح",
                $doctor['name']
            );
        }
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $_SESSION['error_message'] = "حدث خطأ أثناء حذف الطبيب: " . $e->getMessage();
    }
}

header("Location: manage_doctors.php");
exit();