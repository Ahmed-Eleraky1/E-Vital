<?php
session_start();
include 'db.php';
include 'navbar.php';
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_contact'])) {
  $name = htmlspecialchars(trim($_POST['name']));
  $email = htmlspecialchars(trim($_POST['email']));
  $message = htmlspecialchars(trim($_POST['message']));

  $to = "ahmedeleraky70@gmail.com";
  $subject = "رسالة جديدة من موقع الأمراض المزمنة";
  $body = "الاسم: $name\nالإيميل: $email\nالرسالة: $message";
  $headers = "From: $email\r\n";
  $headers .= "Reply-To: $email\r\n";
  $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

  if (mail($to, $subject, $body, $headers)) {
    echo "<script>alert('تم إرسال الرسالة بنجاح!');</script>";
  } else {
    echo "<script>alert('فشل في إرسال الرسالة، حاول مرة أخرى.');</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Vital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Changa:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css" />
  <style>
    .content {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 100px;
      padding: 20px;
      text-align: center;
      transition: transform 0.4s;
    }
    .image-box img {
      width: 100%;
      max-width: 350px;
      border-radius: 50%;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s;
    }
    .image-box img:hover {
      transform: scale(1.05);
    }
    .text-box {
      max-width: 500px;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      margin-left: 20px;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideIn {
      from { opacity: 0; transform: translateX(-50px); }
      to { opacity: 1; transform: translateX(0); }
    }
    .icon-section,
    .tips-section {
      background-color: white;
      padding: 50px 20px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      margin-top: 20px;
    }
    .icon-box {
      padding: 20px;
      border-radius: 10px;
      transition: transform 0.3s ease;
    }
    .icon-box:hover {
      transform: scale(1.1);
    }
    .recipes-section {
      background-color: #fff;
      padding: 60px 20px;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      margin-top: 20px;
    }
    .recipes-section .row {
      display: flex;
      align-items: center;
    }
    .recipe-image img {
      width: 100%;
      border-radius: 15px;
      height: 42.1rem;
    }
    .recipe-icons {
      display: flex;
      flex-direction: column;
      align-items: start;
    }
    .recipe-icons .recipe-box {
      display: flex;
      align-items: center;
      background: #f8f9fa;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 10px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .recipe-icons .recipe-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }
    .recipe-icons i {
      font-size: 30px;
      margin-right: 10px;
    }
    .services-section ul li {
      display: flex;
      align-items: center;
      padding: 10px;
      font-size: 18px;
    }
    .services-section ul li i {
      margin-left: 10px;
      font-size: 22px;
    }
    .logo-box {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .logo-box i {
      font-size: 24px;
      margin-left: 10px;
    }
    .site-name {
      font-size: 20px;
      font-weight: bold;
    }
    .Contact-end h3 {
      display: inline-block;
      font-family: "Poppins", sans-serif;
      margin: 46px 34px 12px;
      padding-top: 28px;
      padding-bottom: 0px;
      font-size: 28px;
    }
    .input input {
      font-size: 17px;
      padding: 8px;
      display: block;
      border: none;
      border-bottom: 1px solid #ccc;
      width: 97%;
      margin: 26px;
    }
    .input button {
      width: 13%;
      background: black;
      color: white;
      border: none;
      position: relative;
      left: -25px;
      top: 14px;
      padding: 12px;
      font-size: 15px;
    }
    .end {
      width: 100%;
      height: 150px;
      background: #cdcdcd;
      position: relative;
      top: 140px;
    }
    .end span {
      display: inline-block;
      width: 100%;
      text-align: center;
      position: relative;
      top: 50%;
      font-size: 23px;
      font-family: system-ui;
      text-decoration: inherit;
    }
    .end a {
      color: black;
    }
    button:hover {
      background: #bdbdbd;
      color: black;
    }
    a:hover,
    .ahmed:hover {
      background: #bdbdbd;
    }
    .mo:hover {
      color: white;
    }
    .cont-pic .pic span:hover {
      background: #bdbdbd;
      color: black;
    }
    .contact-section {
      border-radius: 1.5rem;
      padding: 50px 0;
      background-color: #ffffff;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    .contact-title {
      font-size: 1.8rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 10px;
    }
    .contact-subtitle {
      font-size: 1rem;
      color: #666;
      margin-bottom: 40px;
    }
    .contact-content {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 30px;
    }
    .contact-map {
      text-align: center;
    }
    .map-image {
      width: 100%;
      max-width: 400px;
      height: auto;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .contact-details {
      text-align: right;
    }
    .contact-info-details p {
      font-size: 1rem;
      color: #555;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
    }
    .contact-info-details i {
      color: #555;
      margin-left: 10px;
    }
    .contact-message {
      font-size: 1rem;
      color: #666;
      margin-bottom: 20px;
    }
    .contact-form .form-control {
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 10px;
      font-size: 1rem;
      color: #333;
    }
    .contact-form .form-control:focus {
      outline: none;
      border-color: #333;
      box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
    .contact-form textarea {
      resize: none;
    }
    .btn-send-message {
      background-color: #333;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      font-size: 1rem;
      display: flex;
      align-items: center;
      transition: background-color 0.3s;
    }
    .btn-send-message i {
      margin-left: 8px;
    }
    .btn-send-message:hover {
      background-color: #bdbdbd5e;
    }
    @media (max-width: 767px) {
      .contact-content {
        flex-direction: column;
        text-align: center;
      }
      .contact-details {
        text-align: center;
      }
      .contact-info-details p {
        justify-content: center;
      }
      .contact-info-details i {
        margin-left: 0;
        margin-right: 10px;
      }
    }
  </style>
</head>
<body>
<div class="cont-pic">
  <div class="pic" style="width: 100%;padding: 0 ;margin: 0 ;background-size: cover" >
    <img src="img/cro.jpg" alt="صورة طبية" >
  </div>
</div>

<div class="page-content" id="page-content">
  <main class="main" id="main">
    <div class="content"> 
      <div class="image-box m-5">
        <img src="img/cro3.webp" alt="صورة طبية">
      </div>
      <div>
        <div class="text-box" id="about">
          <h3 class="text-primary">الهدف</h3>
          <p>يهدف هذا الموقع إلى زيادة الوعي حول الأمراض المزمنة وتقديم نصائح وإرشادات للتعامل معها بطريقة صحية.</p>
        </div>
        <div class="text-box mt-4">
          <h3 class="text-primary">سهولة البحث عن بيانات المرضى</h3>
          <p>يتيح النظام للطبيب إمكانية البحث عن بيانات المريض بسهولة عند زيارته لأي عيادة أو مستشفى، مما يساعده على اتخاذ قرارات علاجية أكثر دقة.</p>
        </div>
      </div>
    </div>
  <br>
  <br>
    
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <img src="img/cro5.jpg" class="img-fluid rounded" alt="خدمات الموقع">
        </div>
        <div class="col-md-6 services-section" id="services">
          <h3 class="text-primary me-5">خدماتنا</h3>
          <ul class="list-group">
            <li class="list-group-item"><i class="fas fa-laptop-medical text-primary"></i> الاستشارات الطبية عبر الإنترنت</li>
            <li class="list-group-item"><i class="fas fa-chart-line text-primary"></i> تحليل البيانات الصحية</li>
            <li class="list-group-item"><i class="fas fa-heartbeat text-primary"></i> مراقبة الأمراض المزمنة</li>
            <li class="list-group-item"><i class="fas fa-notes-medical text-primary"></i> توفير خطط علاجية مخصصة</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container my-5 icon-section">
      <h3 class="text-primary">أهمية الفحوصات الطبية</h3>
      <div class="row mt-4">
        <div class="col-md-4 text-center icon-box">
          <i class="fas fa-vials fa-4x text-info"></i>
          <h4 class="mt-3">تحاليل الدم</h4>
          <p>تحليل الدم يساعد في تشخيص الأمراض المزمنة وكشف أي مشاكل صحية مبكرًا.</p>
        </div>
        <div class="col-md-4 text-center icon-box">
          <i class="fas fa-x-ray fa-4x text-danger"></i>
          <h4 class="mt-3">الأشعة</h4>
          <p>الأشعة تساعد في كشف المشكلات الهيكلية داخل الجسم مثل الكسور والأورام.</p>
        </div>
        <div class="col-md-4 text-center icon-box">
          <i class="fas fa-microscope fa-4x text-warning"></i>
          <h4 class="mt-3">تحاليل الميكروبيولوجي</h4>
          <p>تحاليل الميكروبيولوجي تكشف عن الفيروسات والبكتيريا المسببة للأمراض.</p>
        </div>
      </div>
    </div>

    <div class="container my-5 tips-section">
      <h3 class="text-primary">نصائح طبية للحفاظ على صحتك</h3>
      <div class="row mt-4">
        <div class="col-md-4 text-center icon-box">
          <i class="fas fa-apple-alt fa-4x text-success"></i>
          <h4 class="mt-3">تناول غذاء صحي</h4>
          <p>اتباع نظام غذائي متوازن يساعد في تقوية المناعة والوقاية من الأمراض.</p>
        </div>
        <div class="col-md-4 text-center icon-box">
          <i class="fas fa-running fa-4x text-danger"></i>
          <h4 class="mt-3">ممارسة الرياضة</h4>
          <p>التمارين الرياضية المنتظمة تحافظ على اللياقة وتقلل من مخاطر الأمراض المزمنة.</p>
        </div>
        <div class="col-md-4 text-center icon-box">
          <i class="fas fa-bed fa-4x text-warning"></i>
          <h4 class="mt-3">الحصول على قسط كافٍ من النوم</h4>
          <p>النوم الجيد يساعد في تحسين الصحة العقلية والجسدية.</p>
        </div>
      </div>
    </div>

    <div class="container my-5 recipes-section" id="tips">
      <h3 class="text-center text-primary">وصفات طبية منزلية</h3>
      <div class="row mt-4">
        <div class="col-md-6 recipe-image">
          <img src="img/cro7.jpg" alt="وصفات منزلية">
        </div>
        <div class="col-md-6 recipe-icons text-center justify-content-center">
          <div class="recipe-box d-flex align-items-center w-100 text-center justify-content-center">
            <i class="fas fa-seedling me-2 text-primary"></i>
            <p>وصفة العسل والزنجبيل لتقوية المناعة</p>
          </div>
          <div class="recipe-box d-flex align-items-center w-100 text-center justify-content-center">
            <i class="fas fa-lemon me-2 text-primary"></i>
            <p>وصفة الليمون والعسل لتهدئة السعال</p>
          </div>
          <div class="recipe-box d-flex align-items-center w-100 text-center justify-content-center">
            <i class="fas fa-pepper-hot me-2 text-primary"></i>
            <p>وصفة الفلفل الأسود والعسل لتحسين الهضم</p>
          </div>
          <div class="recipe-box d-flex align-items-center w-100 text-center justify-content-center">
            <i class="fas fa-mortar-pestle me-2 text-primary"></i>
            <p>وصفة الكركم والحليب لتخفيف الالتهابات</p>
          </div>
          <div class="recipe-box d-flex align-items-center w-100 text-center justify-content-center">
            <i class="fas fa-carrot me-2 text-primary"></i>
            <p>وصفة الجزر والعسل لتقوية النظر</p>
          </div>
          <div class="recipe-box d-flex align-items-center w-100 text-center justify-content-center">
            <i class="fas fa-apple-alt me-2 text-primary"></i>
            <p>وصفة التفاح والقرفة لتعزيز صحة القلب</p>
          </div>
          <div class="recipe-box d-flex align-items-center w-100 text-center justify-content-center">
            <i class="fas fa-fish me-2 text-primary"></i>
            <p>وصفة زيت السمك لتحسين صحة الدماغ</p>
          </div>
          <div class="recipe-box d-flex align-items-center w-100 text-center justify-content-center">
            <i class="fas fa-mug-hot me-2 text-primary"></i>
            <p>وصفة الشاي الأخضر والنعناع لتحسين الهضم</p>
          </div>
        </div>
      </div>
    </div>
        
    <section class="contact-section container my-5" id="contact">
      <h2 class="contact-title">تواصل معنا</h2>
      <p class="contact-subtitle">نرحب بآرائكم واستفساراتكم!</p>
      <div class="contact-content row">
        <div class="col-md-6 contact-details">
          <div class="contact-info-details">
            <p><i class="fas fa-map-marker-alt me-2"></i>القاهرة، مصر</p>
            <p><i class="fas fa-phone me-2"></i>Phone: +20 01002959192</p>
            <p><i class="fas fa-envelope me-2"></i>Email: ahmeohamed246@gmail.com</p>
          </div>
          <p class="contact-message">اترك لنا رسالة أو استفسار:</p>
          <form method="POST" action="contact.php">
            <div class="row">
              <div class="col-md-6">
                <input type="text" name="name" placeholder="الاسم" required class="form-control mb-3" />
              </div>
              <div class="col-md-6">
                <input type="email" name="email" placeholder="البريد الالكتروني" required class="form-control mb-3" />
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <textarea name="message" placeholder="الرسالة" required class="form-control mb-3" rows="5"></textarea>
              </div>
            </div>
            <button type="submit" name="submit_contact" class="btn btn-send-message"><i class="fas fa-paper-plane me-2"></i>إرسال الرسالة</button>
          </form>
        </div>
      </div>
    </section>

    <div class="container-fluid footer text-center py-3">
      <p>© 2025 جميع الحقوق محفوظة - موقع الأمراض المزمنة</p>
    </div>
  </main>
</div>



</body>
</html>