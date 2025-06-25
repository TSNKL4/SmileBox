<?php
session_start();
require '../includes/db.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    die("⚠️ Error: Session ไม่ถูกต้อง กรุณาเข้าสู่ระบบใหม่ <a href='../login.php'>เข้าสู่ระบบ</a>");
}
$employee_id = $_SESSION['user_id'];

// ตรวจสอบค่า course_id
if (!isset($_POST['course_id'])) {
    die("⚠️ Error: ไม่พบข้อมูลคอร์ส");
}
$course_id = $_POST['course_id'];

// อัปเดตสถานะการเรียนเป็น 'completed'
$sql = "UPDATE enrollments SET status = 'completed' WHERE employee_id = ? AND course_id = ? AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $employee_id, $course_id);

if ($stmt->execute()) {
    echo "<script>alert('📚 อัปเดตสถานะการเรียนเรียบร้อย!'); window.location.href='course_detail.php?course_id=$course_id';</script>";
} else {
    echo "<script>alert('❌ เกิดข้อผิดพลาด โปรดลองอีกครั้ง'); window.location.href='course_detail.php?course_id=$course_id';</script>";
}

$stmt->close();
$conn->close();
?>
