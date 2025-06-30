<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['patient_id']) && !isset($_SESSION['doctor_id'])) {
    echo json_encode(['error' => 'غير مصرح']);
    exit;
}

// تحديد نوع المستخدم وهويته
$user_type = isset($_SESSION['patient_id']) ? 'patient' : 'doctor';
$user_id = isset($_SESSION['patient_id']) ? intval($_SESSION['patient_id']) : intval($_SESSION['doctor_id']);

// جلب الإشعارات غير المقروءة
$sql = "SELECT notification_id, message, type, created_at, is_read 
        FROM notifications 
        WHERE user_id = $user_id 
        AND user_type = '$user_type' 
        ORDER BY created_at DESC 
        LIMIT 10";

$result = $conn->query($sql);
$notifications = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = [
            'id' => $row['notification_id'],
            'message' => $row['message'],
            'type' => $row['type'],
            'created_at' => $row['created_at'],
            'is_read' => (bool)$row['is_read']
        ];
    }
}

// تحديث الإشعارات كمقروءة إذا تم طلب ذلك
if (isset($_GET['mark_as_read']) && $_GET['mark_as_read'] === 'true') {
    $update_sql = "UPDATE notifications 
                   SET is_read = TRUE 
                   WHERE user_id = $user_id 
                   AND user_type = '$user_type' 
                   AND is_read = FALSE";
    $conn->query($update_sql);
}

// إرجاع الإشعارات
echo json_encode([
    'notifications' => $notifications,
    'unread_count' => count(array_filter($notifications, function($n) { return !$n['is_read']; }))
]);