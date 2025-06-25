<?php
session_start();
require '../includes/db.php';

// ตรวจสอบว่า Session ทำงานปกติหรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    die("⚠️ Error: Session ไม่ถูกต้อง กรุณาเข้าสู่ระบบใหม่ <a href='../login.php'>เข้าสู่ระบบ</a>");
}
$employee_id = $_SESSION['user_id'];


if (isset($_GET['id'])) {
    $course_id = intval($_GET['id']); // ป้องกัน SQL Injection

    // ตรวจสอบว่าพนักงานลงทะเบียนคอร์สนี้ไปแล้วหรือไม่
    $sql = "SELECT employee_id FROM enrollments WHERE employee_id = ? AND course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $employee_id, $course_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        echo "<script>alert('❌ คุณได้ลงทะเบียนคอร์สนี้ไปแล้ว!'); window.location.href='employee_dashboard.php';</script>";
        exit();
    }
    $stmt->close();

    // ลงทะเบียนคอร์ส
    $sql = "INSERT INTO enrollments (employee_id, course_id, enrollment_date, progress, status) VALUES (?, ?, NOW(), 0, 'ongoing')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $employee_id, $course_id);

    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>alert('✅ ลงทะเบียนสำเร็จ!'); window.location.href='Course_Learning.php';</script>";
        exit();
    } else {
        error_log("SQL Error: " . $stmt->error, 0);
        echo "<script>alert('❌ เกิดข้อผิดพลาดในการลงทะเบียน'); window.location.href='employee_dashboard.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('⚠️ ไม่มีคอร์สที่ระบุ'); window.location.href='employee_dashboard.php';</script>";
    exit();
}

$conn->close();
?>
