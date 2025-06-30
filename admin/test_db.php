<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';

// Simple query to check doctors table
$test_query = "SELECT COUNT(*) as total FROM doctors";
$result = $conn->query($test_query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$row = $result->fetch_assoc();
echo "Total doctors in database: " . $row['total'];
?>
