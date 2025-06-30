<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

if (isset($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Get patient details first for the success message
        $sql = "SELECT name FROM patients WHERE patient_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();
        
        // Delete related records first to maintain referential integrity
        $tables = [
            'appointments',
            'prescriptions',
            'labtests',
            'patient_xrays',
            'medical_records',
            'patient_doctors'
        ];
        
        foreach ($tables as $table) {
            // Check if table exists before attempting to delete
            $check_table = $conn->query("SHOW TABLES LIKE '$table'");
            if ($check_table->num_rows > 0) {
                $sql = "DELETE FROM $table WHERE patient_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $patient_id);
                $stmt->execute();
            }
        }
        
        // Finally, delete the patient
        $sql = "DELETE FROM patients WHERE patient_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $patient_id);
        
        if ($stmt->execute()) {
            // Commit transaction
            $conn->commit();
            $_SESSION['success_message'] = sprintf(
                "تم حذف المريض %s وجميع سجلاته المرتبطة بنجاح",
                $patient['name']
            );
        }
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $_SESSION['error_message'] = "حدث خطأ أثناء حذف المريض: " . $e->getMessage();
    }
}

header("Location: manage_patients.php");
exit();