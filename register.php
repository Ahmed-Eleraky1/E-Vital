<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // التحقق من تطابق كلمات المرور
    if ($password == $confirm_password) {
        // التحقق من عدم تكرار البريد الإلكتروني
        $check_sql = "SELECT * FROM patients WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['error'] = "هذا البريد الإلكتروني مستخدم بالفعل";
        } else {
            // إضافة المريض الجديد
            $sql = "INSERT INTO patients (name, date_of_birth, gender, contact_number, email, password) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $name, $date_of_birth, $gender, $contact_number, $email, $password);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "تم التسجيل بنجاح. يمكنك الآن تسجيل الدخول";
                header('Location: login.php');
                exit();
            } else {
                $_SESSION['error'] = "حدث خطأ أثناء التسجيل. الرجاء المحاولة مرة أخرى";
            }
        }
    } else {
        $_SESSION['error'] = "كلمات المرور غير متطابقة";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل مريض جديد - الأمراض المزمنة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Changa', sans-serif;
            background: #041951;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
        }
        .form-control:focus {
            border-color: #041951;
            box-shadow: #041951;
        }
        .btn-primary {
            background-color: #041951;
            border-color: #041951;
        }
        .btn-primary:hover {
            background-color: #bdbdbd5e;
            color: #041951;
            border-color: #041951;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1 class="text-center mb-4">تسجيل مريض جديد</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="name" class="form-label">الاسم الكامل</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="date_of_birth" class="form-label">تاريخ الميلاد</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
            </div>

            <div class="mb-3">
                <label for="gender" class="form-label">الجنس</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="">اختر الجنس</option>
                    <option value="Male">ذكر</option>
                    <option value="Female">أنثى</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="contact_number" class="form-label">رقم الهاتف</label>
                <input type="tel" class="form-control" id="contact_number" name="contact_number" required>
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
                <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">تسجيل الحساب</button>
            
            <div class="text-center mt-3" >
                <p>لديك حساب بالفعل؟ <a href="login.php" style="text-decoration: none">تسجيل الدخول</a></p>
            </div>
        </form>
    </div>
</body>
</html>
