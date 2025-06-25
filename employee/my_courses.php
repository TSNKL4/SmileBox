<?php
session_start();
include('../includes/db.php'); // <-- ตรวจสอบ path ให้ถูก

$employee_id = $_SESSION['user_id']; // ดึงจาก session login

$sql = "SELECT c.course_id, c.course_name, c.duration, c.description, c.image_path, e.progress 
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        WHERE e.employee_id = ? AND e.status = 'active'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>คอร์สของฉัน</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h2>คอร์สที่คุณลงทะเบียนแล้ว</h2>
  <div class="row">
    <?php while($row = $result->fetch_assoc()): ?>
      <div class="col-md-4 mb-4">
        <div class="card">
          <img src="<?= $row['image_path'] ?>" class="card-img-top" alt="รูปคอร์ส">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['course_name']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
            <p>ระยะเวลา: <?= $row['duration'] ?> ชั่วโมง</p>
            <p>ความคืบหน้า: <?= $row['progress'] ?>%</p>
            <a href="course_lesson.php?course_id=<?= $row['course_id'] ?>" class="btn btn-primary">เข้าสู่บทเรียน</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>
</body>
</html>
