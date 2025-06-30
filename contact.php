<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$response = ''; // متغير لتخزين رسالة النجاح أو الخطأ
$show_form = true; // التحكم في إظهار النموذج أو رسالة النجاح

if (isset($_POST['submit_contact'])) {
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        // إعداد SMTP مع Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ahmeohamed246@gmail.com'; // ✳️ إيميلك
        $mail->Password   = 'qjrk vjsg gvvw ehsj';   // ✳️ كلمة مرور تطبيق من جوجل
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->CharSet = 'UTF-8';

        // معلومات الرسالة
        $mail->setFrom($email, $name);
        $mail->addAddress('ahmeohamed246@gmail.com', 'Chronic Diseases'); // ✳️ الإيميل اللي تستقبل عليه

        $mail->Subject = "رسالة من الموقع";
        $mail->Body    = "الاسم: $name\nالبريد: $email\n\nالرسالة:\n$message";

        $mail->send();
        $response = "تم إرسال الرسالة بنجاح!";
        $show_form = false; // إخفاء النموذج وعرض رسالة النجاح
    } catch (Exception $e) {
        $response = "حدث خطأ: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اتصل بنا</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .success-message {
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 1s ease-in-out;
        }
        .success-message svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        .success-message h2 {
            color: #28a745;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: fadeIn 1s ease-in-out;
        }
        .back-button {
            display: inline-block;
            padding: 12px 25px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 16px;
            transition: transform 0.3s, background 0.3s;
        }
        .back-button:hover {
            background: #0056b3;
            transform: scale(1.05);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input, textarea {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
            transition: border 0.3s;
        }
        input:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        button {
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #218838;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($show_form): ?>
            <h2>اتصل بنا</h2>
            <!-- عرض رسالة الخطأ إن وجدت -->
            <?php if (!empty($response) && strpos($response, 'خطأ') !== false): ?>
                <div class="error-message">
                    <?php echo $response; ?>
                </div>
            <?php endif; ?>
            <!-- نموذج الاتصال -->
            <form method="POST" action="">
                <label for="name">الاسم:</label>
                <input type="text" id="name" name="name" required>
                <label for="email">البريد الإلكتروني:</label>
                <input type="email" id="email" name="email" required>
                <label for="message">الرسالة:</label>
                <textarea id="message" name="message" required></textarea>
                <button type="submit" name="submit_contact">إرسال</button>
            </form>
        <?php else: ?>
            <!-- رسالة النجاح -->
            <div class="success-message">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#28a745">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                <h2>تم إرسال الرسالة بنجاح!</h2>
                <a href="index.php" class="back-button">الرجوع للصفحة الرئيسية</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>