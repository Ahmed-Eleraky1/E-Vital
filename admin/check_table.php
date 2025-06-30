<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php';

// Check if the doctors table exists and its structure
$check_table = "SHOW TABLES LIKE 'doctors'";
$result = $conn->query($check_table);

if ($result->num_rows == 0) {
    echo "The doctors table does not exist!";
} else {
    echo "The doctors table exists.<br>";
    
    // Check table structure
    $structure = "DESCRIBE doctors";
    $result = $conn->query($structure);
    
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    
    echo "<br>Table structure:<br>";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
}
?>
