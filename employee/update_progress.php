<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    die("⚠️ Session ไม่ถูกต้อง");
}

// ตรวจสอบค่าที่ส่งมาครบไหม
$enrollment_id = $_POST['enrollment_id'] ?? null;
$lesson_id = $_POST['lesson_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$enrollment_id || !$lesson_id || !$status) {
    die("⚠️ ข้อมูลไม่ครบ");
}

$date = date("Y-m-d H:i:s");

// อัปเดต progress
$sql = "INSERT INTO learning_progress (enrollment_id, lesson_id, date, status) 
        VALUES (?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE status=?, date=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iissss", $enrollment_id, $lesson_id, $date, $status, $status, $date);
$stmt->execute();
$stmt->close();

echo "success";
?>
