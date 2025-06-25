<?php
session_start();

// ตรวจสอบว่าเป็นผู้ดูแลระบบ (admin) หรือไม่
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../login.php");
    exit();
}
include("../includes/db.php");

$query = "SELECT c.course_id, c.course_name, COUNT(e.employee_id) as num_registered, e.status
          FROM courses c
          LEFT JOIN enrollments e ON c.course_id = e.course_id
          GROUP BY c.course_id, c.course_name, e.status";
$result_courses = mysqli_query($conn, $query);

// if ($result_courses && mysqli_num_rows($result_courses) > 0) {
//     while ($row = mysqli_fetch_assoc($result_courses)) {
//         // ตรวจสอบการเข้าถึงข้อมูล
//         echo "Course ID: " . $row['course_id'] . "<br>";
//         echo "Course Name: " . $row['course_name'] . "<br>";
//         echo "Number of Registrations: " . $row['num_registered'] . "<br>";
//         echo "Enrollment Status: " . $row['status'] . "<br>";
//     }
// } else {
//     echo "ไม่พบข้อมูลคอร์ส";
// }

// Fetch learning progress data
$query_progress = "SELECT e.employee_id, c.course_name, e.progress 
                   FROM enrollments e
                   LEFT JOIN courses c ON e.course_id = c.course_id";
$result_progress = mysqli_query($conn, $query_progress);

// // Fetch course registrations data
// $query_registrations = "SELECT e.employee_id, c.course_name, e.enrollment_date
//                        FROM enrollments e
//                        LEFT JOIN courses c ON e.course_id = c.course_id";
// $result_registrations = mysqli_query($conn, $query_registrations);

// if ($result_registrations && mysqli_num_rows($result_registrations) > 0) {
//     while ($row = mysqli_fetch_assoc($result_registrations)) {
//         echo "Employee ID: " . $row['employee_id'] . "<br>";
//         echo "Course Name: " . $row['course_name'] . "<br>";
//         echo "Enrollment Date: " . $row['enrollment_date'] . "<br><br>";
//     }
// } else {
//     echo "ไม่พบข้อมูลการลงทะเบียน";
// }
// ?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดผู้สอน - Dental Course</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/CSS/styles.css">
    <link rel="stylesheet" href="../assets/CSS/admin_styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Navbar ด้านบน -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid d-flex justify-content-between align-items-center">
    <img src="../assets/images/logo.jpg" alt="Logo" width="40" height="40" class="me-2">
        <a class="navbar-brand" href="lecturer_dashboard.php">Dentistry Courses</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto gap-2">
                <li class="nav-item"><a class="nav-link" href="#">My Course</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Courses</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
            </ul>
             <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <?= htmlspecialchars($_SESSION['username'] ?? 'Lecturer') ?> 
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">โปรไฟล์ของฉัน</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../logout.php">ออกจากระบบ</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Layout -->
<div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar d-flex flex-column flex-shrink-0 position-fixed">
        <div class="sidebar-header">
            <h4 class="fw-bold mb-0">Lecturer</h4>
            <p class="text-muted small">Dashboard</p>
        </div>
        <!-- <div class="nav flex-column">
            <a href="lecturer_dashboard.php" class="sidebar-link active text-decoration-none p-3">
                <i class="fas fa-home me-3"></i>
                <span class="hide-on-collapse">Dashboard</span>
            </a>
            <a class="sidebar-link text-decoration-none p-3 d-flex justify-content-between align-items-center"
                 data-bs-toggle="collapse" href="#manageUsersCollapse" role="button">
                <div>
                    <i class="fas fa-user-plus me-3"></i>
                    <span class="hide-on-collapse">จัดการข้อมูล</span>
                </div>
                <i class="fas fa-chevron-down"></i>
            </a>
            <div class="collapse ps-4" id="manageUsersCollapse">
                <a href="#" class="sidebar-link text-decoration-none p-2 d-block">ข้อมูลผู้ใช้งาน</a>
                <a href="#" class="sidebar-link text-decoration-none p-2 d-block">ข้อมูลคอร์ส</a>
                <!-- <a href="#" class="sidebar-link text-decoration-none p-2 d-block">ข้อมูลบทเรียน</a> -->
            </div>
            
            <!-- <a class="sidebar-link text-decoration-none p-3 d-flex justify-content-between align-items-center"
                 data-bs-toggle="collapse" href="#viewDataCollapse" role="button">
                <div>
                    <i class="fas fa-book-open me-3"></i>
                    <span class="hide-on-collapse">ดูข้อมูล</span>
                </div>
                <i class="fas fa-chevron-down"></i>
            </a>
            <div class="collapse ps-4" id="viewDataCollapse">
                <a href="#" class="sidebar-link text-decoration-none p-2 d-block">ข้อมูลพนักงาน</a>
                <a href="a#" class="sidebar-link text-decoration-none p-2 d-block">ข้อมูลผู้สอน</a>
                <a href="#" class="sidebar-link text-decoration-none p-2 d-block">ข้อมูลคอร์ส</a>
            </div>
            <a href="#" class="sidebar-link text-decoration-none p-3">
                <i class="fas fa-file-alt me-3"></i>
                <span class="hide-on-collapse">ข้อมูลการลงทะเบียนเรียน</span>
            </a> -->
        </div> 
    </nav>
</div>

<!-- Main Content -->
<main class="main-content">
    <div class="container-fluid">
    <div class="text1" style="margin-top: 5%;">
        <h2>ยินดีต้อนรับสู่แดชบอร์ดผู้สอน</h2>
        <p class="text-muted">ระบบบริหารจัดการคอร์สออนไลน์</p> 
    </div>
    
        <!-- รายงานข้อมูลการเข้าเรียนรายบุคคล -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">รายงานข้อมูลการเข้าเรียนรายบุคคล</h5>
            </div>
            <div class="card-body">
                <canvas id="individualAttendanceChart" width="200" height="100"></canvas>
            </div>
        </div>
        <style>
            .card {
                max-width: 700px; /* ลดขนาดกรอบของรายงานให้เล็กลง */
                margin: auto; /* จัดให้อยู่ตรงกลาง */
            }
            .card-header {
                padding: 10px;
                font-size: 16px;
            }
            .card-body {
                padding: 15px;
                font-size: 14px;
            }
            .table {
                font-size: 14px;
            }
        </style>
        <!-- รายงานข้อมูลคอร์สทั้งหมด -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">รายงานข้อมูลคอร์สทั้งหมด</h5>
            </div>
            <div class="card-body">
            <table class="table table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>ชื่อคอร์ส</th>
            <th>จำนวนผู้ลงทะเบียน</th>
        </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($result_courses) > 0) {
                    while ($row = mysqli_fetch_assoc($result_courses)) { ?>
                        <tr>
                            <td><?= $row['course_id'] ?></td>
                            <td><?= htmlspecialchars($row['course_name']) ?></td>
                            <td><?= $row['num_registered'] ?></td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr>
                        <td colspan="3" class="text-center">ไม่มีข้อมูลคอร์ส</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
            </div>
        </div>

        <!-- รายงานข้อมูลความก้าวหน้าในการเรียน -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">รายงานข้อมูลความก้าวหน้าในการเรียน</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ผู้เรียน</th>
                            <th>ความก้าวหน้า  (%)</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result_progress)) { ?>
                            <tr>
                                <td><?= $row['employee_id'] ?></td>
                                <td><?= htmlspecialchars($row['course_name']) ?></td>
                                <td><?= $row['progress'] ?>%</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- รายงานข้อมูลคอร์สที่มีการลงทะเบียน -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">รายงานข้อมูลคอร์สที่มีการลงทะเบียน</h5>
            </div>
            <div class="card-body">
            <table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>ชื่อคอร์ส</th>
            <th>จำนวนผู้ลงทะเบียน</th>
            <th>สถานะ</th>
        </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($result_courses) > 0) {
                    while ($row = mysqli_fetch_assoc($result_courses)) { ?>
                        <tr>
                            <td><?= $row['course_id'] ?></td>
                            <td><?= htmlspecialchars($row['course_name']) ?></td>
                            <td><?= $row['num_registered'] ?></td>
                            <td>
                                <?php 
                                if ($row['enrollment_status'] === null) {
                                    echo "ไม่มีข้อมูล";
                                } else {
                                    switch ($row['enrollment_status']) {
                                        case 'active': 
                                            echo "กำลังเรียน"; 
                                            break;
                                        case 'completed': 
                                            echo "เรียนจบแล้ว"; 
                                            break;
                                        case 'inactive': 
                                            echo "ไม่ได้เรียน"; 
                                            break;
                                        default: 
                                            echo "ไม่มีข้อมูล";
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr>
                        <td colspan="4" class="text-center">ไม่มีข้อมูลคอร์ส</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
            </div>
        </div>
    </div>
    
</main>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // กราฟข้อมูลการเข้าเรียนรายบุคคล
    const ctx = document.getElementById('individualAttendanceChart').getContext('2d');
    const individualAttendanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['User1', 'User2', 'User3', 'User4'], // ตัวอย่างข้อมูล
            datasets: [{
                label: 'ชั่วโมงการเข้าเรียน',
                data: [12, 15, 8, 10], // ตัวอย่างข้อมูล
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html> 