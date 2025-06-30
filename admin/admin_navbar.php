<?php
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);


if (!isset($admin_name)) {
    include '../db.php';
    $admin_id = $_SESSION['admin_id'];
    $sql_admin = "SELECT name FROM admins WHERE admin_id = '$admin_id'";
    $admin_result = $conn->query($sql_admin);
    $admin_name = $admin_result && $admin_result->num_rows > 0 ? 
    $admin_result->fetch_assoc()['name'] : 'مدير';
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@300;400;500;700&display=swap" rel="stylesheet">
        <style>
        body {
        background: linear-gradient(to right, #d0e5ff, #e1f0ff);
        font-family: 'Changa', sans-serif;
        }
        .sidebar {
            background: linear-gradient(180deg, #1e3a8a 0%, #3b82f6 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .sidebar a {
            transition: all 0.2s ease;
            text-decoration: none;        }
        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(-5px);
        }
        </style>
</head>
<body>
<div class="flex min-h-screen">
        <!-- Sidebar -->
    <div class="sidebar fixed top-0 bottom-0 right-0 w-64 p-6 text-white z-50">
    <h2 class="text-2xl font-bold mb-8 text-center">
        <?php echo htmlspecialchars($admin_name); ?>
    </h2>
    <nav>
        <ul class="space-y-4">
            <li>
                <a href="dashboard.php" class="flex items-center p-3 rounded-lg">
                    <i class="fas fa-home ml-2"></i> الرئيسية
                </a>
            </li>
            <li>
                <a href="admin_profile.php" class="flex items-center p-3 rounded-lg hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-user-circle ml-2"></i> الملف الشخصي
                </a>
            </li>
            <li>
                <a href="manage_patients.php" class="flex items-center p-3 rounded-lg hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-user-plus ml-2"></i> إدارة المرضى
                </a>
            </li>
            <li>
                <a href="manage_doctors.php" class="flex items-center p-3 rounded-lg hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-user-md ml-2"></i> إدارة الأطباء
                </a>
            </li>
            <li>
                <a href="manage_appointments.php" class="flex items-center p-3 rounded-lg hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-calendar-alt ml-2"></i> إدارة المواعيد
                </a>
            </li>
            <li>
                <a href="system_settings.php" class="flex items-center p-3 rounded-lg hover:bg-white hover:bg-opacity-10 <?php echo $current_page == 'system_settings.php' ? 'bg-opacity-20' : ''; ?>">
                    <i class="fas fa-cogs ml-2"></i> إعدادات النظام
                </a>
            </li>
            <li>
                <a href="reports.php" class="flex items-center p-3 rounded-lg hover:bg-white hover:bg-opacity-10 <?php echo $current_page == 'reports.php' ? 'bg-opacity-20' : ''; ?>">
                    <i class="fas fa-chart-bar ml-2"></i> التقارير
                </a>
            </li>
            <li>
                <a href="../logout.php" class="flex items-center p-3 rounded-lg hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-sign-out-alt ml-2"></i> تسجيل الخروج
                </a>
            </li>
        </ul>
    </nav>
</div>



</body>
</html>
<!-- Success/Error Messages -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
        <?php 
        echo $_SESSION['success_message'];
        unset($_SESSION['success_message']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
        <?php 
        echo $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>