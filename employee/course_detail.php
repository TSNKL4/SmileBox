<?php
session_start();
require '../includes/db.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    die("⚠️ กรุณาเข้าสู่ระบบใหม่ <a href='../login.php'>เข้าสู่ระบบ</a>");
}
$employee_id = $_SESSION['user_id'];

// ตรวจสอบว่ามีคอร์สที่เลือกหรือไม่
if (!isset($_GET['course_id'])) {
    echo "<script>alert('ไม่พบข้อมูลคอร์ส'); window.location.href='employee_dashboard.php';</script>";
    exit();
}

$course_id = $_GET['course_id'];

// ตรวจสอบว่าพนักงานลงทะเบียนคอร์สนี้หรือไม่
$sql = "SELECT c.* FROM enrollments e 
        JOIN courses c ON e.course_id = c.course_id 
        WHERE e.employee_id = ? AND e.course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $employee_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

if (!$course) {
    echo "<script>alert('❌ คุณไม่ได้ลงทะเบียนคอร์สนี้'); window.location.href='employee_dashboard.php';</script>";
    exit();
}

// ดึงเนื้อหาคอร์สจาก lesson
$sql = "SELECT * FROM lessons WHERE course_id = ? ORDER BY order_number";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$lessons = $stmt->get_result();
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
        body { font-family: Arial, sans-serif; background-color: #f8f8f8; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        .lesson-box { background: #f9f9f9; padding: 15px; margin-bottom: 15px; border-radius: 8px; border-left: 5px solid #2196F3; }
        h1, h2, h3 { color: #333; }
        video { width: 100%; border-radius: 8px; }
        a { color: #2196F3; text-decoration: none; font-weight: bold; }
        img { width: 100%; max-height: 200px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($course['course_name']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
        <hr>
        <h2>เนื้อหาคอร์ส</h2>
        <?php while ($lesson = $lessons->fetch_assoc()): ?>
            <div class="lesson-box">
                <?php if (!empty($lesson['image'])): ?>
                    <img src="<?php echo htmlspecialchars($lesson['image']); ?>" alt="Lesson Image">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($lesson['title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($lesson['description'])); ?></p>
                <?php if (!empty($lesson['video_url'])): ?>
                    <video controls>
                        <source src="<?php echo htmlspecialchars($lesson['video_url']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
                <?php if (!empty($lesson['document_url'])): ?>
                    <p><a href="<?php echo htmlspecialchars($lesson['document_url']); ?>" target="_blank">📄 ดาวน์โหลดเอกสาร</a></p>
                <?php endif; ?>
                <p><strong>ระยะเวลา:</strong> <?php echo htmlspecialchars($lesson['duration']); ?> นาที</p>
            </div>
        <?php endwhile; ?>
        <hr>
        <a href="employee_dashboard.php">🔙 กลับไปหน้าหลัก</a>
    </div>
</body>
</html>
