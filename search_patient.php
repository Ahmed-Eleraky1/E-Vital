<?php
include 'db.php';
session_start();

// التحقق من تسجيل دخول الدكتور
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// استرجاع بيانات الدكتور من قاعدة البيانات
$sql = "SELECT name FROM doctors WHERE doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    $_SESSION['error'] = "لم يتم العثور على بيانات الدكتور.";
    header("Location: login.php");
    exit();
}

$search_query = "";
$patients = [];

if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT DISTINCT p.* 
            FROM patients p 
            JOIN patient_doctors pd ON p.patient_id = pd.patient_id 
            WHERE pd.doctor_id = ? AND 
            (p.name LIKE ? OR 
             p.contact_number LIKE ? OR 
             p.email LIKE ?)";
    
    $stmt = $conn->prepare($sql);
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("isss", $doctor_id, $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
} else {
    // إذا لم يتم إدخال بحث، اعرض جميع المرضى المسجلين مع الدكتور
    $sql = "SELECT DISTINCT p.* 
            FROM patients p 
            JOIN patient_doctors pd ON p.patient_id = pd.patient_id 
            WHERE pd.doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>البحث عن المريض</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@300;400&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
    <style>
        body {
        font-family: 'Changa', sans-serif;
        font-weight: 300;
        }
    .btn-login {
      background-color: #041951;
      color: white;
      font-weight: bold;
      border: none;
      padding: 10px 20px;
      border-radius: 30px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      font-weight: 500;

    }

    .btn-login:hover {
      background-color: #8b9bb0;
      transform: scale(1.05);
    }
    </style>
   
</head>

<body class="bg-light">
        <nav class="nav" id="nav">
    <div class="nav-menu nav-container" id="nav-menu">
      <div class="nav-shape"></div>
      <div class="nav-close" id="nav-close">
        <i class="bx bx-x"></i>
      </div>
      <div class="nav-data mt-5">
        <!-- <div class="nav-mask">
          <img src="https://i.postimg.cc/PqfBmCCM/protfolio_img.jpg" alt="" class="nav-img" />
        </div> -->
        <span class="nav-greeting">مرحبًا</span>
        <h1 class="nav-name">
        <?php echo htmlspecialchars($doctor['name']); ?>
        </h1>
      </div>
      <ul class="nav-list">
        <li class="nav-item">
          <a href="index.php #home" class="nav-link active-link">
            <i class="bx bx-home"></i> الرئيسية
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php #about" class="nav-link">
            <i class="bx bx-user"></i> الهدف
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php #services" class="nav-link">
            <i class="bx bx-briefcase-alt-2"></i> الخدمات
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php #tips" class="nav-link">
            <i class="bx bx-bookmark"></i> وصفات طبية
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php #contact" class="nav-link">
            <i class="bx bx-message-square-detail"></i> تواصل معنا
          </a>
        </li>
        <?php if (isset($_SESSION['email'])): ?>
        <li class="nav-item">
          <a href="logout.php" class="nav-link">
            <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>

  <header class="header" id="header">
      <nav class="header-nav container">
        <div class="logo-box">
          <i class="fa-solid fa-disease text-danger fa-2x"></i>
          <span class="header-logo site-name">Electronic Vital</span>
        </div>
        <div class="d-flex align-items-center">
          <?php if (isset($_SESSION['email'])): ?>
            <?php if (isset($_SESSION['doctor_id'])): ?>
              <a href="doctor_profile.php" class="btn btn-login mx-2" style="position: relative; left: 65rem;">الملف الشخصي للطبيب</a>
              <!-- <a href="search_patient.php" class="btn btn-login mx-2" style="position: relative; left: 67.5rem;">البحث عن مريض</a> -->
            <?php else: ?>
              <!-- <a href="profile.php" class="btn btn-login mx-2">الملف الشخصي</a> -->
            <?php endif; ?>
          <?php else: ?>
            <a href="login.php" class="btn btn-login mx-2" style="position: relative; left: 71.5rem;">LOG IN</a>
          <?php endif; ?>          
          <div class="header-toggle" id="header-toggle">
            <i class="bx bx-grid-alt"></i>
          </div>
        </div>
      </nav>
    </header>

  <br>
    <br>
    <div class="container mt-5">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <h1>البحث عن المريض</h1>
        <form id="searchForm" class="mb-4">
            <input type="text" id="search_query" name="search_query" class="form-control" placeholder="أدخل اسم المريض أو رقم المريض أو البريد الإلكتروني">
            <button type="submit" class="btn btn-info mt-2">بحث</button>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>تاريخ الميلاد</th>
                    <th>الجنس</th>
                    <th>البريد الإلكتروني</th>
                    <th>التحكم</th>
                </tr>
            </thead>
            <tbody id="patientsTableBody">
                <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?php echo htmlspecialchars($patient['name']); ?></td>
                    <td><?php echo htmlspecialchars($patient['date_of_birth']); ?></td>
                    <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                    <td>
                        <a href="view_patient.php?patient_id=<?php echo $patient['patient_id']; ?>" class="btn btn-info" >عرض</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const searchQuery = document.getElementById('search_query').value;

        fetch('ajax_search_patient.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `search_query=${encodeURIComponent(searchQuery)}`
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('patientsTableBody').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
    });
    </script>
    <script src="js/main.js"></script>

</body>
</html>
