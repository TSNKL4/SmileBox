<?php
include('../includes/db.php');
session_start();

$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    echo "กรุณาเข้าสู่ระบบก่อนดูรายงานผล";
    exit;
}

// ดึงคอร์สทั้งหมดที่ลงทะเบียนไว้
$sql_courses = "SELECT c.course_id, c.course_name 
                FROM enrollments e
                JOIN courses c ON e.course_id = c.course_id
                WHERE e.employee_id = ?";
$stmt = mysqli_prepare($conn, $sql_courses);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$courses = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
  <title>รายงานผลการเรียน</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      background-color: #f7f7f7;
      color: #333;
      padding: 40px 20px;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      padding: 30px;
    }

    h2 {
      font-size: 24px;
      font-weight: 600;
      color: #2d3748;
      margin-bottom: 30px;
      text-align: center;
    }

    .course-report {
      background-color: #fafafa;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .course-report:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .course-report h3 {
      font-size: 18px;
      font-weight: 600;
      color: #4a5568;
      margin-bottom: 10px;
    }

    .course-report p {
      font-size: 14px;
      color: #718096;
      margin-bottom: 12px;
    }

    .progress-bar {
      background-color: #e2e8f0;
      border-radius: 4px;
      height: 8px;
      overflow: hidden;
      position: relative;
    }

    .progress-fill {
      background-color: #68d391;
      height: 100%;
      border-radius: 4px;
      transition: width 0.5s ease-in-out;
    }

    hr {
      border: 0;
      border-top: 1px solid #edf2f7;
      margin: 20px 0;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>📋 รายงานผลการเรียนของคุณ</h2>

  <?php while ($course = mysqli_fetch_assoc($courses)): ?>
    <div class="course-report">
      <h3><?= htmlspecialchars($course['course_name']) ?></h3>

      <?php
      $cid = $course['course_id'];

      // ดึงจำนวนบทเรียนทั้งหมดในคอร์สนี้
      $total_query = "SELECT COUNT(*) AS total FROM lesson WHERE course_id = ?";
      $stmt_total = mysqli_prepare($conn, $total_query);
      mysqli_stmt_bind_param($stmt_total, "i", $cid);
      mysqli_stmt_execute($stmt_total);
      $total_result = mysqli_stmt_get_result($stmt_total);
      $total = mysqli_fetch_assoc($total_result)['total'];
      mysqli_free_result($total_result);

      // ดึงจำนวนบทเรียนที่เรียนจบแล้ว
      $completed_query = "SELECT COUNT(*) AS completed
                          FROM learning_progress lp
                          JOIN lesson l ON lp.lesson_id = l.lesson_id
                          JOIN enrollments e ON lp.enrollment_id = e.enrollment_id
                          WHERE e.employee_id = ? AND l.course_id = ? AND lp.status = 'Completed'";
      $stmt_completed = mysqli_prepare($conn, $completed_query);
      mysqli_stmt_bind_param($stmt_completed, "ii", $user_id, $cid);
      mysqli_stmt_execute($stmt_completed);
      $completed_result = mysqli_stmt_get_result($stmt_completed);
      $completed = mysqli_fetch_assoc($completed_result)['completed'];
      mysqli_free_result($completed_result);

      $progress = ($total > 0) ? round(($completed / $total) * 100, 1) : 0;
      ?>

      <p>เรียนจบแล้ว <?= $completed ?> จาก <?= $total ?> บทเรียน (<?= $progress ?>%)</p>

      <!-- แถบแสดงเปอร์เซ็นต์ -->
      <div class="progress-bar">
        <div class="progress-fill" style="width: <?= $progress ?>%;"></div>
      </div>
    </div>
    <hr>
  <?php endwhile; ?>

</div>
<?php mysqli_close($conn); ?>
</body>
</html>