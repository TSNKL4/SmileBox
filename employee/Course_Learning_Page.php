<?php
session_start();
require '../includes/db.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    die("⚠️ Error: Session ไม่ถูกต้อง กรุณาเข้าสู่ระบบใหม่ <a href='../login.php'>เข้าสู่ระบบ</a>");
}
$employee_id = $_SESSION['user_id'];

// ตรวจสอบว่ามีคอร์สที่เลือกหรือไม่
if (!isset($_GET['course_id'])) {
    echo "<script>alert('ไม่พบข้อมูลคอร์ส'); window.location.href='employee_dashboard.php';</script>";
    exit();
}

$course_id = $_GET['course_id'];

// ดึงข้อมูลคอร์ส
$sql = "SELECT * FROM courses WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

if (!$course) {
    echo "<script>alert('ไม่พบคอร์สที่เลือก'); window.location.href='employee_dashboard.php';</script>";
    exit();
}

// ดึงเนื้อหาของคอร์ส
$sql = "SELECT * FROM course_contents WHERE course_id = ? ORDER BY content_order";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$contents = $stmt->get_result();
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['course_name']); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .course-box {
            background: #dff0d8;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 5px solid #4CAF50;
        }
        h1, h2, h3 {
            color: #333;
        }
        video {
            width: 100%;
            border-radius: 8px;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($course['course_name']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
        <hr>
        <h2>เนื้อหาคอร์ส</h2>
        <?php while ($content = $contents->fetch_assoc()): ?>
            <div class="course-box">
                <h3><?php echo htmlspecialchars($content['title']); ?></h3>
                <?php if (!empty($content['video_url'])): ?>
                    <video controls>
                        <source src="<?php echo htmlspecialchars($content['video_url']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
                <?php if (!empty($content['pdf_url'])): ?>
                    <p><a href="<?php echo htmlspecialchars($content['pdf_url']); ?>" target="_blank">📄 ดาวน์โหลดเอกสาร</a></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
        <hr>
        <a href="employee_dashboard.php">🔙 กลับไปหน้าหลัก</a>
    </div>
</body>
</html>
