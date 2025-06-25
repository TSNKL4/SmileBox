<?php
include('../includes/db.php'); // <-- ตรวจสอบ path ให้ถูก

// ดึงคอร์สล่าสุด
$sql = "SELECT * FROM courses ORDER BY course_id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $course = $result->fetch_assoc();
    $course_id = $course['course_id'];
} else {
    die("ไม่พบคอร์สในระบบ");
}

// ดึงคอนเทนต์ของคอร์สนี้
$sql_contents = "SELECT * FROM lesson WHERE course_id = $course_id";
$contents = $conn->query($sql_contents);
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
            font-family: "Sarabun", sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 960px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            color: #b1005f;
        }
        .content-box {
            background-color: #ffe6f0;
            border-left: 10px solid #d63384;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 12px;
        }
        .content-box h3 {
            margin: 0;
            color: #c2185b;
        }
        .video-count, .pdf-count {
            margin-top: 8px;
            font-size: 14px;
            color: #555;
        }
        video {
            margin-top: 10px;
            max-width: 100%;
            border-radius: 8px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($course['course_name']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
        <hr>
        <h2>🧭 Learning Path</h2>
        <?php
        $index = 0;
        while ($content = $contents->fetch_assoc()):
            $video = !empty($content['video_url']);
            $pdf = !empty($content['document_url']);
        ?>
            <div class="content-box">
                <strong>บทที่ <?php echo $index; ?>:</strong>
                <h3><?php echo htmlspecialchars($content['title']); ?></h3>
                <div class="video-count">🎥 วิดีโอ: <?php echo $video ? 1 : 0; ?> | 📄 เอกสาร: <?php echo $pdf ? 1 : 0; ?></div>

                <?php if ($video): ?>
                    <video controls>
                        <source src="<?php echo htmlspecialchars($content['video_url']); ?>" type="video/mp4">
                        เบราว์เซอร์ของคุณไม่รองรับวิดีโอ
                    </video>
                <?php endif; ?>

                <?php if ($pdf): ?>
                    <p><a href="<?php echo htmlspecialchars($content['document_url']); ?>" target="_blank">📥 ดาวน์โหลดเอกสาร</a></p>
                <?php endif; ?>
            </div>
        <?php
            $index++;
        endwhile;
        ?>
        <a class="back-link" href="employee_dashboard.php">⬅️ กลับหน้าหลัก</a>
    </div>
</body>
</html>
