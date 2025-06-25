<?php
require '../includes/db.php';

// กำหนดค่าเริ่มต้นให้ตัวแปรทั้งหมด
$course_id = $course_name = $duration = $description = $image_path = $lecturer_name = $category_name = "ไม่ระบุ";

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $course_id = intval($_GET['id']); // แปลงเป็นตัวเลขเพื่อป้องกัน SQL Injection

    $sql = "SELECT c.course_id, c.course_name, c.duration, c.description, c.image_path, 
                   CONCAT(l.first_name, ' ', l.last_name) AS lecturer_name, 
                   cat.category_name 
            FROM courses c
            LEFT JOIN lecturer l ON c.lecturer_id = l.lecturer_id
            LEFT JOIN category cat ON c.category_id = cat.category_id
            WHERE c.course_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $stmt->store_result();

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($course_id, $course_name, $duration, $description, $image_path, $lecturer_name, $category_name);
            $stmt->fetch();
        } else {
            echo "<script>alert('ไม่พบข้อมูลคอร์ส'); window.history.back();</script>";
            exit();
        }
        $stmt->close();
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการดึงข้อมูลคอร์ส'); window.history.back();</script>";
        exit();
    }
} else {
    echo "<script>alert('ไม่มีการส่งค่า ID มา'); window.history.back();</script>";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดคอร์ส</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/CSS/styles.css">
    <style>
        body {
            background-color: #ffe6ea;
        }

        .course-container {
            max-width: 400px; /* ลดขนาดจาก 500px เป็น 400px */
            margin: 30px auto; /* ลด margin ด้านบน */
            background: white;
            padding: 15px; /* ลด padding ลง */
            border-radius: 12px;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1); /* ลดเงาลงเล็กน้อย */
            text-align: center;
        }

        .course-image {
            width: 100%;
            height: auto;
            border-radius: 10px; /* ปรับให้ขอบมนเล็กน้อย */
            object-fit: cover;
        }

        .category-badge {
            background-color: #f4b400;
            color: white;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 14px;
        }

        .register-button {
            display: block;
            width: 100%;
            background-color: rgb(2, 212, 135); /* สีเขียวอ่อน */
            color: white;
            border: none;
            padding: 10px;  /* ลดขนาดปุ่มลง */
            border-radius: 15px; /* ลดความโค้งมนลงเล็กน้อย */
            font-size: 16px; /* ลดขนาดตัวอักษร */
            margin-top: 15px;
            text-decoration: none;
            cursor: pointer;
        }

        .register-button:hover {
            background-color: rgb(33, 185, 129); /* สีเขียวเข้มขึ้นเมื่อโฮเวอร์ */
        }

    </style>
</head>
<body>

<div class="course-container">
    <?php 
    $image_src = !empty($image_path) ? "../uploads/" . $image_path : "../assets/images/default_course.jpg";
    ?>
    <img src="<?= htmlspecialchars($image_src) ?>" class="course-image" alt="<?= htmlspecialchars($course_name) ?>">

    <h2><?= htmlspecialchars($course_name) ?></h2>
    <p><strong>โดย:</strong> <?= htmlspecialchars($lecturer_name) ?></p>
    <p><strong>รหัส:</strong> <?= htmlspecialchars($course_id) ?></p>
    <p><strong>หมวดหมู่:</strong> <span class="category-badge"><?= htmlspecialchars($category_name) ?></span></p>

    <h4>ลงทะเบียนเรียน</h4>
    <button class="register-button" onclick="confirmRegistration(<?= $course_id ?>)">ลงทะเบียนตอนนี้</button>
</div>

<script>
    function confirmRegistration(courseId) {
        if (confirm("คุณต้องการลงทะเบียนคอร์สนี้หรือไม่?")) {
            window.location.href = "employee_register_course.php?id=" + courseId;
        }
    }
</script>

</body>
</html>
