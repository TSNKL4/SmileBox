<?php
session_start();

// ตรวจสอบว่าเป็นพนักงาน (employee) หรือไม่
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../login.php"); // หากไม่ใช่พนักงานให้กลับไปหน้า login
    exit();
}

include("../includes/db.php"); // เชื่อมต่อฐานข้อมูล
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดพนักงาน - Dental Course</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/CSS/styles.css">
</head>
<body>
    
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- โลโก้ -->
        <img src="../assets/images/logo.jpg" alt="Logo" width="40" height="40" class="me-2">
        <a class="navbar-brand" href="employee_dashboard.php">Dentistry Courses</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- เมนูตรงกลาง -->
            <ul class="navbar-nav mx-auto gap-2">
                <li class="nav-item">
                    <a class="nav-link" href="my_courses.php">My Course</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Course_Learning_Page.php">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Course_Learning.php">Contact</a>
                </li>
            </ul>
            
            <!-- เมนู Dropdown -->
            <ul class="navbar-nav d-flex align-items-center ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= htmlspecialchars($_SESSION['username'] ?? 'employee') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="employee_profile.php">โปรไฟล์ของฉัน</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../logout.php">ออกจากระบบ</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Dashboard Content -->
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <h1>ยินดีต้อนรับสู่แดชบอร์ดพนักงาน</h1>
    </div>

    <!-- ส่วนแสดงข้อมูลคอร์สในแดชบอร์ด -->
    <div class="mt-4">
        <h3>ข้อมูลคอร์ส</h3>
        <div class="row">
            <?php
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $query = "SELECT * FROM courses";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $image_path = !empty($row['image_path']) ? "../uploads/" . $row['image_path'] : "../assets/images/default_course.jpg"; // ใช้รูปเริ่มต้นถ้าไม่มีภาพ
                    echo "<div class='col-md-4 mb-4'>
                            <div class='card' style='width: 18rem;'>
                                <img src='$image_path' class='card-img-top' alt='" . htmlspecialchars($row['course_name']) . "' style='height: 180px; object-fit: cover;'>
                                <div class='card-body'>
                                    <h5 class='card-title'>" . htmlspecialchars($row['course_name']) . "</h5>
                                    <p class='card-text'>" . htmlspecialchars($row['description']) . "</p>
                                    <a href='employee_view_course.php?id=" . $row['course_id'] . "' class='btn btn-primary'>ดูรายละเอียด</a>
                                </div>
                            </div>
                        </div>";
                }
            } else {
                echo "<div class='col-12'><p class='text-center'>ไม่มีคอร์สที่จะแสดง</p></div>";
            }
            ?>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
