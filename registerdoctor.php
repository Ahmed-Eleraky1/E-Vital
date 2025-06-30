<?php
session_start();
include 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone'];
    $specialty = $_POST['specialty'];
    $address = $_POST['address'];
    $qualification = $_POST['qualification'];
    $experience = $_POST['experience'];

    // Validate passwords
    if ($password != $confirm_password) {
        $error = "كلمات المرور غير متطابقة";
    } else {
        // Removed password hashing
        // Check if email already exists
        $check_email = "SELECT * FROM doctors WHERE email = '$email'";
        $result = $conn->query($check_email);
        
        if ($result->num_rows > 0) {
            $error = "هذا البريد الإلكتروني مستخدم بالفعل";
        } else {
            // Insert doctor data
            $sql = "INSERT INTO doctors (name, email, password, contact_number, specialty, address, qualification, experience) 
                    VALUES ('$name', '$email', '$password', '$phone', '$specialty', '$address', '$qualification', '$experience')";
            
            if ($conn->query($sql) === TRUE) {
                header('Location: login.php');
                exit();
            } else {
                $error = "حدث خطأ أثناء إنشاء الحساب: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دكتور - الأمراض المزمنة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Changa', sans-serif;
            background: #041951;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .register-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: #041951;
            max-width: 800px;
            margin: 0 auto;
        }
        .form-control:focus {
            border-color:#041951;
            box-shadow: #041951;
        }
        .btn-primary {
            background-color:#041951;
            border-color:#041951;
        }
        .btn-primary:hover {
            background-color: #bdbdbd5e;
            border-color: #041951;
            color:#041951;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1 class="text-center mb-4">تسجيل دكتور جديد</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">الاسم الكامل</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">رقم الهاتف</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="specialty" class="form-label">التخصص</label>
                    <select class="form-control" id="specialty" name="specialty" required>
                        <option value="">اختر التخصص</option>
                        <option value="القلب">طب القلب</option>
                        <option value="الجراحة">الجراحة</option>
                        <option value="الباطنية">طب الباطنة</option>
                        <option value="العظام">طب العظام</option>
                        <option value="الجهاز الهضمي">طب الجهاز الهضمي</option>
                        <option value="الجهاز التنفسي">طب الجهاز التنفسي</option>
                        <option value="الكلى">طب الكلى</option>
                        <option value="السكري">طب السكري</option>
                        <option value="الجهاز العصبي">طب الجهاز العصبي</option>
                        <option value="الأورام">طب الأورام</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">العنوان</label>
                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="qualification" class="form-label">المؤهلات العلمية</label>
                <textarea class="form-control" id="qualification" name="qualification" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="experience" class="form-label">سنوات الخبرة</label>
                <input type="number" class="form-control" id="experience" name="experience" min="0" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">إنشاء الحساب</button>
            <div class="text-center mt-3" >
                <p>لديك حساب بالفعل؟ <a href="login.php" style="text-decoration: none">تسجيل الدخول</a></p>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>