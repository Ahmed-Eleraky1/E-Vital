<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_type'])) {
    $user_type = $_POST['user_type'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($user_type == 'admin') {
        $sql = "SELECT * FROM admins WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['email'] = $email;
            $_SESSION['user_type'] = 'admin';
            $_SESSION['admin_id'] = $row['admin_id'];
            header('Location: admin/dashboard.php');
            exit;
        }
    } elseif ($user_type == 'patient') {
        $sql = "SELECT * FROM patients WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['email'] = $email;
            $_SESSION['user_type'] = 'patient';
            $_SESSION['patient_id'] = $row['patient_id'];
            header('Location: profile.php');
            exit;
        }
    } elseif ($user_type == 'doctor') {
        $sql = "SELECT * FROM doctors WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['email'] = $email;
            $_SESSION['user_type'] = 'doctor';
            $_SESSION['doctor_id'] = $row['doctor_id'];
            header('Location: doctor_profile.php');
            exit;
        }
    }
    
    $_SESSION['error'] = "البريد الإلكتروني أو كلمة المرور غير صحيحة";
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOG IN E-Vital</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com"> -->
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@600;700&display=swap" rel="stylesheet">
    <style>
           body {
            font-family: 'Changa', sans-serif;
            font-weight: 500;
            background-image: url('img/pngtree-pulmonologists-check-lungs-affected-by-covid19-and-other-diseases-vector-png-image_12550387.png');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(40px);
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            text-align: center;
            animation: slideIn 0.6s ease-out;
            border: 1px solid #2239b1;
        }
        .login-container h3 {
            color: #2239b1;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 0.5rem;
            color: #2239b1;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2239b1;
            background: rgba(255, 255, 255, 0.2);
        }
        .form-group label {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            color: #2239b1;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            pointer-events: none;
        }
        .form-group input:focus + label,
        .form-group input:not(:placeholder-shown) + label,
        .form-group select:focus + label,
        .form-group select:not(:placeholder-shown) + label {
            top: -1.2rem;
            right: 0.5rem;
            font-size: 0.75rem;
            color: #2239b1;
        }
        .btn-login {
            background: #2239b1;
            color: white;
            padding: 0.75rem;
            border-radius: 0.5rem;
            width: 100%;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background: #2239b1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        .alert {
            background: rgba(239, 68, 68, 0.9);
            color: white;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            animation: fadeIn 0.5s ease;
        }
        .links a {
            color: #2239b1;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .links a:hover {
            color: #2239b1;
            text-decoration: underline;
        }
        .p{
            color: #587fb3d4;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <h3>تسجيل الدخول</h3>
        <form action="login.php" method="post">
            <div class="form-group">
                <select name="user_type" class="form-control" required>
                    <option value="">-- نوع المستخدم --</option>
                    <option value="doctor">دكتور</option>
                    <option value="patient">مرضى</option>
                    <option value="admin">مدير النظام</option>
                </select>
            </div>

            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder=" " required>
                <label>البريد الإلكتروني</label>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder=" " required>
                <label>كلمة المرور</label>
            </div>
            <button type="submit" class="btn btn-login">تسجيل الدخول</button>
            <div class="links mt-4 text-gray-300">
                <p class="p">ليس لديك حساب؟ <a href="register.php">إنشاء حساب مريض</a></p>
                <p class="p">ليس لديك حساب دكتور؟ <a href="registerdoctor.php">إنشاء حساب دكتور</a></p>
            </div>
        </form>
    </div>
    <script>
        // إضافة تأثيرات على الإنبوتس عند التركيز أو إلغاء التركيز
        document.querySelectorAll('.form-group input, .form-group select').forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('input-focused');
            });
            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('input-focused');
            });
        });
    </script>
</body>
</html>