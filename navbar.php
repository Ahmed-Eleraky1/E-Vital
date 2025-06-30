<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
    <title>E-Vital</title>
</head>
<body>

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
          <span class="header-logo site-name"> Electronic Vital</span>
        </div>
        <div class="d-flex align-items-center">
          <?php if (isset($_SESSION['email'])): ?>
            <?php if (isset($_SESSION['doctor_id'])): ?>
              <a href="doctor_profile.php " class="btn btn-login mx-2" style="position: relative; left: 65rem;">الملف الشخصي للطبيب</a>
            <?php else: ?>
              <a href="profile.php" class="btn btn-login mx-2" style="position: relative; left: 67.5rem;">الملف الشخصي</a>
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
    <div class="container-fluid p-0 ">
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
  <script src="js/main.js"></script>

    
</body>
</html>