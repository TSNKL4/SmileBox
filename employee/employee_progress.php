<?php
require '../includes/db.php';

// ดึงรายชื่อพนักงานและความคืบหน้าในคอร์ส
$sql = "
SELECT e.employee_id, e.first_name, e.last_name, c.course_name, en.progress
FROM enrollments en
JOIN employee e ON en.employee_id = e.employee_id
JOIN courses c ON en.course_id = c.course_id
ORDER BY e.employee_id, c.course_id
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ความคืบหน้าของพนักงาน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f1f1;
            padding: 30px;
        }
        .container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .progress {
            height: 25px;
            border-radius: 50px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">📊 ความคืบหน้าการเรียนของพนักงาน</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): 
            $progress = (int)$row['progress'];
            // กำหนดสีตามระดับ progress
            if ($progress == 100) {
                $color = 'bg-danger';  // แดง
            } elseif ($progress >= 75) {
                $color = 'bg-warning'; // เหลือง
            } elseif ($progress >= 50) {
                $color = 'bg-info';    // ฟ้า
            } else {
                $color = 'bg-success'; // เขียว
            }
        ?>
            <div class="mb-4">
                <h6><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?> - <strong><?= htmlspecialchars($row['course_name']) ?></strong></h6>
                <div class="progress">
                    <div class="progress-bar <?= $color ?>" role="progressbar" style="width: <?= $progress ?>%" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                        <?= $progress ?>%
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>ไม่พบข้อมูลความคืบหน้า</p>
    <?php endif; ?>
</div>
</body>
</html>
