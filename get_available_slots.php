<?php
header('Content-Type: application/json');
include 'db.php';

if (!isset($_GET['doctor_id']) || !isset($_GET['date'])) {
    echo json_encode(['error' => 'البيانات المطلوبة غير مكتملة']);
    exit;
}

$doctor_id = intval($_GET['doctor_id']);
$selected_date = mysqli_real_escape_string($conn, $_GET['date']);
$day_of_week = date('w', strtotime($selected_date)); // 0 = Sunday, 6 = Saturday

// الحصول على ساعات عمل الطبيب لليوم المحدد
$working_hours_sql = "SELECT start_time, end_time, is_working 
                     FROM doctor_working_hours 
                     WHERE doctor_id = $doctor_id 
                     AND day = $day_of_week";
$working_hours_result = $conn->query($working_hours_sql);
$doctor_schedule = $working_hours_result->fetch_assoc();

if (!$doctor_schedule || !$doctor_schedule['is_working']) {
    echo json_encode([
        'available_slots' => [],
        'message' => 'الطبيب غير متاح في هذا اليوم'
    ]);
    exit;
}

$start_time = $doctor_schedule['start_time'];
$end_time = $doctor_schedule['end_time'];

// الحصول على المواعيد المحجوزة في التاريخ المحدد
$booked_slots_sql = "SELECT appointment_date 
                     FROM appointments 
                     WHERE doctor_id = $doctor_id 
                     AND DATE(appointment_date) = '$selected_date'
                     AND status IN ('approved', 'pending')
                     ORDER BY appointment_date";

$booked_slots_result = $conn->query($booked_slots_sql);
$booked_slots = [];

while ($row = $booked_slots_result->fetch_assoc()) {
    $booked_slots[] = date('H:i', strtotime($row['appointment_date']));
}

// إنشاء قائمة بجميع المواعيد المتاحة (كل 30 دقيقة)
$available_slots = [];
$current_time = strtotime($selected_date . ' ' . $start_time);
$end_timestamp = strtotime($selected_date . ' ' . $end_time);

while ($current_time < $end_timestamp) {
    $slot_time = date('H:i', $current_time);
    
    // التحقق من أن الموعد غير محجوز
    $is_available = true;
    foreach ($booked_slots as $booked_slot) {
        $time_diff = abs(strtotime($slot_time) - strtotime($booked_slot)) / 60;
        if ($time_diff < 30) { // موعد محجوز أو قريب جداً من موعد محجوز
            $is_available = false;
            break;
        }
    }

    if ($is_available) {
        $available_slots[] = $slot_time;
    }

    $current_time += 1800; // إضافة 30 دقيقة
}

echo json_encode([
    'available_slots' => $available_slots,
    'doctor_working_hours' => [
        'start' => date('H:i', strtotime($start_time)),
        'end' => date('H:i', strtotime($end_time))
    ]
]);