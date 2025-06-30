<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$xray_id = $_GET['id'];
$patient_id = $_GET['patient_id'];
$doctor_id = $_SESSION['doctor_id'];

// التحقق من أن الأشعة تنتمي إلى مريض الدكتور
$check_sql = "SELECT px.*, xt.xray_name, xt.description, xt.radiation_level, xt.required_preparation 
              FROM patient_xrays px 
              JOIN xraytypes xt ON px.xray_type_id = xt.xray_type_id
              JOIN patient_doctors pd ON px.patient_id = pd.patient_id
              WHERE px.patient_xray_id = ? AND pd.doctor_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $xray_id, $doctor_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: search_patient.php");
    exit();
}

$xray = $result->fetch_assoc();

// جلب أنواع الأشعة
$sql_xray_types = "SELECT * FROM xraytypes";
$xray_types_result = $conn->query($sql_xray_types);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $xray_type_id = $_POST['xray_type_id'];
    $test_date = $_POST['test_date'];
    $notes = $_POST['notes'];

    $update_sql = "UPDATE patient_xrays SET xray_type_id = ?, test_date = ?, notes = ? WHERE patient_xray_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("issi", $xray_type_id, $test_date, $notes, $xray_id);
    
    if ($update_stmt->execute()) {
        header("Location: view_patient.php?patient_id=" . $patient_id);
        exit();
    } else {
        $error = "حدث خطأ أثناء تحديث الأشعة";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الأشعة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h1>تعديل الأشعة</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="xray_type_id" class="form-label">نوع الأشعة</label>
                <select class="form-control" id="xray_type_id" name="xray_type_id" required>
                    <?php while($row = $xray_types_result->fetch_assoc()): ?>
                        <option value="<?php echo $row['xray_type_id']; ?>" 
                                <?php echo ($row['xray_type_id'] == $xray['xray_type_id']) ? 'selected' : ''; ?>>
                            <?php echo $row['xray_name']; ?> 
                            (مستوى الإشعاع: <?php echo $row['radiation_level']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="test_date" class="form-label">تاريخ الأشعة</label>
                <input type="date" class="form-control" id="test_date" name="test_date" value="<?php echo $xray['test_date']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">الملاحظات</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $xray['notes']; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">تحديث الأشعة</button>
            <a href="view_patient.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</body>
</html>