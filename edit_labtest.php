<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$test_id = $_GET['id'];
$patient_id = $_GET['patient_id'];
$doctor_id = $_SESSION['doctor_id'];

// التحقق من أن التحليل ينتمي إلى مريض الدكتور
$check_sql = "SELECT l.*, lt.test_name, lt.description, lt.normal_range, lt.unit 
              FROM labtests l 
              JOIN labtesttypes lt ON l.test_type_id = lt.test_type_id
              JOIN patient_doctors pd ON l.patient_id = pd.patient_id 
              WHERE l.test_id = ? AND pd.doctor_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $test_id, $doctor_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: search_patient.php");
    exit();
}

$test = $result->fetch_assoc();

// جلب أنواع الاختبارات المخبرية
$sql_test_types = "SELECT * FROM labtesttypes";
$test_types_result = $conn->query($sql_test_types);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test_type_id = $_POST['test_type'];
    $test_date = $_POST['test_date'];
    $result_value = $_POST['result'];
    $notes = $_POST['notes'];

    $update_sql = "UPDATE labtests SET test_type_id = ?, test_date = ?, result = ?, notes = ? WHERE test_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("isssi", $test_type_id, $test_date, $result_value, $notes, $test_id);
    
    if ($update_stmt->execute()) {
        header("Location: view_patient.php?patient_id=" . $patient_id);
        exit();
    } else {
        $error = "حدث خطأ أثناء تحديث الاختبار المخبري";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الاختبار المخبري</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h1>تعديل الاختبار المخبري</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="test_type" class="form-label">نوع الاختبار</label>
                <select class="form-control" id="test_type" name="test_type" required>
                    <?php while($row = $test_types_result->fetch_assoc()): ?>
                        <option value="<?php echo $row['test_type_id']; ?>" <?php echo ($row['test_type_id'] == $test['test_type_id']) ? 'selected' : ''; ?>>
                            <?php echo $row['test_name']; ?>
                            <?php if ($row['normal_range']): ?>
                                (مدى طبيعي: <?php echo $row['normal_range']; ?> <?php echo $row['unit']; ?>)
                            <?php endif; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="test_date" class="form-label">تاريخ الاختبار</label>
                <input type="date" class="form-control" id="test_date" name="test_date" value="<?php echo $test['test_date']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="result" class="form-label">النتيجة</label>
                <input type="text" class="form-control" id="result" name="result" value="<?php echo $test['result']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">الملاحظات</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $test['notes']; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">تحديث الاختبار</button>
            <a href="view_patient.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</body>
</html>