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

// Get overall statistics
$sql_stats = "SELECT
    (SELECT COUNT(*) FROM patients) as total_patients,
    (SELECT COUNT(*) FROM doctors) as total_doctors,
    (SELECT COUNT(*) FROM appointments) as total_appointments,
    (SELECT COUNT(*) FROM prescriptions) as total_prescriptions,
    (SELECT COUNT(*) FROM labtests) as total_labtests,
    (SELECT COUNT(*) FROM patient_xrays) as total_xrays";
$stats = $conn->query($sql_stats)->fetch_assoc();

// Get monthly appointments trend for the last 6 months
$sql_monthly = "SELECT 
    DATE_FORMAT(appointment_date, '%Y-%m') as month,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
FROM appointments 
WHERE appointment_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
GROUP BY DATE_FORMAT(appointment_date, '%Y-%m')
ORDER BY month DESC";
$monthly_result = $conn->query($sql_monthly);

// Get top doctors by appointments
$sql_top_doctors = "SELECT 
    d.name,
    d.specialty,
    COUNT(a.appointment_id) as total_appointments,
    COUNT(DISTINCT p.patient_id) as total_patients
FROM doctors d
LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
LEFT JOIN patients p ON a.patient_id = p.patient_id
GROUP BY d.doctor_id
ORDER BY total_appointments DESC
LIMIT 5";
$top_doctors = $conn->query($sql_top_doctors);

// Get patient age distribution
$sql_age_groups = "SELECT 
    CASE 
        WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18 THEN 'أقل من 18'
        WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 18 AND 30 THEN '18-30'
        WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 31 AND 50 THEN '31-50'
        WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 51 AND 70 THEN '51-70'
        ELSE 'أكثر من 70'
    END as age_group,
    COUNT(*) as count
FROM patients
GROUP BY age_group
ORDER BY 
    CASE age_group
        WHEN 'أقل من 18' THEN 1
        WHEN '18-30' THEN 2
        WHEN '31-50' THEN 3
        WHEN '51-70' THEN 4
        ELSE 5
    END";
$age_groups = $conn->query($sql_age_groups);

// Get common medical conditions
$sql_conditions = "SELECT 
    diagnosis,
    COUNT(*) as count
FROM medicalrecords
WHERE diagnosis IS NOT NULL
GROUP BY diagnosis
ORDER BY count DESC
LIMIT 10";
$conditions = $conn->query($sql_conditions);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقارير والإحصائيات - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        Chart.register(ChartDataLabels);
    </script>
    <style>
        .stat-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .stat-card .card-body {
            padding: 1.5rem;
        }
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.8;
            transition: all 0.3s ease;
        }
        .stat-card:hover i {
            transform: scale(1.1);
            opacity: 1;
        }
        .stat-card .counter {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #2196F3, #3f51b5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: countUp 2s ease-out;
        }
        .stat-card p {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container-fluid mt-4">
        <!-- Overall Statistics -->
         <div class="mr-64 p-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-3xl font-bold text-gray-800">التقارير والإحصائيات</h1>
        </div>
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card stat-card bg-gradient">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x text-primary mb-3"></i>
                        <h3 class="counter"><?php echo $stats['total_patients']; ?></h3>
                        <p class="mb-0 text-muted">المرضى</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card bg-gradient">
                    <div class="card-body text-center">
                        <i class="fas fa-user-md fa-2x text-success mb-3"></i>
                        <h3 class="counter"><?php echo $stats['total_doctors']; ?></h3>
                        <p class="mb-0 text-muted">الأطباء</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card bg-gradient">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-2x text-info mb-3"></i>
                        <h3 class="counter"><?php echo $stats['total_appointments']; ?></h3>
                        <p class="mb-0 text-muted">المواعيد</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card bg-gradient">
                    <div class="card-body text-center">
                        <i class="fas fa-prescription fa-2x text-warning mb-3"></i>
                        <h3 class="counter"><?php echo $stats['total_prescriptions']; ?></h3>
                        <p class="mb-0 text-muted">الوصفات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card bg-gradient">
                    <div class="card-body text-center">
                        <i class="fas fa-flask fa-2x text-danger mb-3"></i>
                        <h3 class="counter"><?php echo $stats['total_labtests']; ?></h3>
                        <p class="mb-0 text-muted">الفحوصات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card bg-gradient">
                    <div class="card-body text-center">
                        <i class="fas fa-x-ray fa-2x text-secondary mb-3"></i>
                        <h3 class="counter"><?php echo $stats['total_xrays']; ?></h3>
                        <p class="mb-0 text-muted">الأشعة</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Monthly Appointments Chart -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">اتجاهات المواعيد الشهرية</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="appointmentsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Age Distribution Chart -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">توزيع أعمار المرضى</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Top Doctors -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">أفضل الأطباء أداءً</h5>
                    </div>
                    <div class="card-body "style="margin-bottom: 2.4rem;">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>الطبيب</th>
                                        <th>التخصص</th>
                                        <th>المرضى</th>
                                        <th>المواعيد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($doctor = $top_doctors->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                                            <td><?php echo htmlspecialchars($doctor['specialty']); ?></td>
                                            <td><?php echo $doctor['total_patients']; ?></td>
                                            <td><?php echo $doctor['total_appointments']; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Common Medical Conditions -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">الحالات المرضية الشائعة</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="conditionsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Register ChartJS plugins
    Chart.register(ChartDataLabels);

    // Monthly Appointments Chart
    new Chart(document.getElementById('appointmentsChart'), {
        type: 'line',
        data: {
            labels: [<?php 
                $months = [];
                $totals = [];
                $completed = [];
                $cancelled = [];
                while($row = $monthly_result->fetch_assoc()) {
                    $months[] = "'" . date('F Y', strtotime($row['month'] . '-01')) . "'";
                    $totals[] = $row['total'];
                    $completed[] = $row['completed'];
                    $cancelled[] = $row['cancelled'];
                }
                echo implode(',', array_reverse($months));
            ?>],
            datasets: [{
                label: 'إجمالي المواعيد',
                data: [<?php echo implode(',', array_reverse($totals)); ?>],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }, {
                label: 'المواعيد المكتملة',
                data: [<?php echo implode(',', array_reverse($completed)); ?>],
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }, {
                label: 'المواعيد الملغاة',
                data: [<?php echo implode(',', array_reverse($cancelled)); ?>],
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    display: function(context) {
                        return context.dataset.data[context.dataIndex] > 0;
                    },
                    color: function(context) {
                        return context.dataset.borderColor;
                    },
                    anchor: 'end',
                    align: 'top',
                    offset: 4,
                    font: {
                        weight: 'bold',
                        size: 11
                    },
                    formatter: Math.round
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Age Distribution Chart
    new Chart(document.getElementById('ageChart'), {
        type: 'pie',
        data: {
            labels: [<?php 
                $age_labels = [];
                $age_data = [];
                while($row = $age_groups->fetch_assoc()) {
                    $age_labels[] = "'" . $row['age_group'] . "'";
                    $age_data[] = $row['count'];
                }
                echo implode(',', $age_labels);
            ?>],
            datasets: [{
                data: [<?php echo implode(',', $age_data); ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ],
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    display: function(context) {
                        return context.dataset.data[context.dataIndex] > 0;
                    },
                    color: function(context) {
                        return context.dataset.borderColor;
                    },
                    anchor: 'end',
                    align: 'top',
                    offset: 4,
                    font: {
                        weight: 'bold',
                        size: 11
                    },
                    formatter: Math.round
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Medical Conditions Chart
    new Chart(document.getElementById('conditionsChart'), {
        type: 'bar',
        data: {
            labels: [<?php 
                $condition_labels = [];
                $condition_data = [];
                while($row = $conditions->fetch_assoc()) {
                    $condition_labels[] = "'" . $row['diagnosis'] . "'";
                    $condition_data[] = $row['count'];
                }
                echo implode(',', $condition_labels);
            ?>],
            datasets: [{
                label: 'عدد المرضى',
                data: [<?php echo implode(',', $condition_data); ?>],
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                datalabels: {
                    display: function(context) {
                        return context.dataset.data[context.dataIndex] > 0;
                    },
                    anchor: 'end',
                    align: 'end',
                    color: '#000',
                    font: {
                        weight: 'bold',
                        size: 11
                    },
                    formatter: Math.round
                },
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    </script>
    
</body>
</html>