<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db.php';
$admin_id = $_SESSION['admin_id'];
$sql_admin = "SELECT name FROM admins WHERE admin_id = '$admin_id'";
$admin_result = $conn->query($sql_admin);
$admin_name = $admin_result && $admin_result->num_rows > 0 ? $admin_result->fetch_assoc()['name'] : 'مدير';

$specialties = [
    'طب عام',
    'طب القلب',
    'طب الأطفال',
    'طب النساء والتوليد',
    'طب العيون',
    'طب الأعصاب',
    'طب الأسنان',
    'طب العظام',
    'طب الجلدية',
    'طب النفسي',
    'طب الباطني',
    'طب المسالك البولية'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $contact_number = $_POST['phone'] ?? '';
    $specialty = $_POST['specialty'] ?? '';
    $qualification = $_POST['qualification'] ?? '';
    $experience = intval($_POST['experience'] ?? 0);
    $address = $_POST['address'] ?? '';
    $hospital_affiliation = $_POST['hospital_affiliation'] ?? '';
    $working_hours = $_POST['working_hours'] ?? [];
    $working_hours_json = json_encode($working_hours);

    // Check if email already exists
    $sql = "SELECT doctor_id FROM doctors WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "البريد الإلكتروني مستخدم بالفعل";
    } else {
        // Insert new doctor
        $sql = "INSERT INTO doctors (name, email, password, contact_number, specialty, qualification, experience, address, hospital_affiliation, working_hours, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssisss", $name, $email, $password, $contact_number, $specialty, $qualification, $experience, $address, $hospital_affiliation, $working_hours_json);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "تمت إضافة الطبيب بنجاح";
            header("Location: manage_doctors.php");
            exit();
        } else {
            $error_message = "حدث خطأ أثناء إضافة الطبيب: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة طبيب جديد - لوحة التحكم</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <style>
        .working-hours {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .day-schedule {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<?php include 'admin_navbar.php'; ?>

<div class="container mt-4">
            <h1 class="text-3xl font-bold text-gray-800"
            style="margin-right: 24rem; margin-bottom: 2rem; margin-top: 1rem;"
            > اضافة طبيب</h1>
            
    <div class="row justify-content-center">
        <div class="col-md-8 mr-64">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">إضافة طبيب جديد</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الطبيب</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>

                        <div class="mb-3">
                            <label for="specialty" class="form-label">التخصص</label>
                            <select class="form-select" id="specialty" name="specialty" required>
                                <option value="">اختر التخصص</option>
                                <?php foreach ($specialties as $specialty): ?>
                                    <option value="<?php echo $specialty; ?>"><?php echo $specialty; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="qualification" class="form-label">المؤهل العلمي</label>
                            <input type="text" class="form-control" id="qualification" name="qualification" required>
                        </div>

                        <div class="mb-3">
                            <label for="experience" class="form-label">سنوات الخبرة</label>
                            <input type="number" class="form-control" id="experience" name="experience" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">العنوان</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>

                        <div class="mb-3">
                            <label for="hospital_affiliation" class="form-label">الجهة الطبية المنتسب إليها</label>
                            <input type="text" class="form-control" id="hospital_affiliation" name="hospital_affiliation" required>
                        </div>

                        <div class="working-hours">
                            <h5>ساعات العمل</h5>
                            <?php
                            $days = [
                                'sunday' => 'الأحد',
                                'monday' => 'الاثنين',
                                'tuesday' => 'الثلاثاء',
                                'wednesday' => 'الأربعاء',
                                'thursday' => 'الخميس',
                                'friday' => 'الجمعة',
                                'saturday' => 'السبت'
                            ];
                            foreach ($days as $day_en => $day_ar):
                            ?>
                                <div class="day-schedule">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input"
                                               id="<?= $day_en ?>_enabled"
                                               name="working_hours[<?= $day_en ?>][enabled]" value="1">
                                        <label class="form-check-label" for="<?= $day_en ?>_enabled"><?= $day_ar ?></label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">من</label>
                                            <input type="time" class="form-control"
                                                   name="working_hours[<?= $day_en ?>][start]" value="09:00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">إلى</label>
                                            <input type="time" class="form-control"
                                                   name="working_hours[<?= $day_en ?>][end]" value="17:00">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> إضافة الطبيب
                            </button>
                            <a href="manage_doctors.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
