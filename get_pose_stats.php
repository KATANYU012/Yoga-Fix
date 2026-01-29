<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");

// ความปลอดภัย: ตรวจสอบว่าเป็นแอดมินหรือไม่
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Access Denied."]);
    exit();
}

$servername = "localhost"; 
$username_db = "root"; 
$password_db = ""; 
$dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
mysqli_set_charset($conn, "utf8mb4");

if ($conn->connect_error) {
    http_response_code(500);
    exit();
}

// SQL Query หัวใจสำคัญของการคำนวณ
// - GROUP BY pose_name: เพื่อรวมข้อมูลของแต่ละท่า
// - COUNT(*): นับจำนวนแถวทั้งหมด (จำนวนครั้งที่ฝึก)
// - SUM(CASE...): นับเฉพาะแถวที่ result คือ 'correct'
$sql = "
    SELECT 
        pose_name, 
        COUNT(*) as total_attempts, 
        SUM(CASE WHEN result = 'correct' THEN 1 ELSE 0 END) as correct_attempts 
    FROM user_progress 
    GROUP BY pose_name
";

$result = $conn->query($sql);
$stats_arr = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // คำนวณเปอร์เซ็นต์ความถูกต้อง
        $accuracy = 0;
        if ($row['total_attempts'] > 0) {
            $accuracy = ($row['correct_attempts'] / $row['total_attempts']) * 100;
        }

        // เพิ่มข้อมูลที่คำนวณแล้วเข้าไปใน array
        $stats_arr[] = [
            'pose_name' => $row['pose_name'],
            'total_attempts' => (int)$row['total_attempts'],
            'correct_attempts' => (int)$row['correct_attempts'],
            'accuracy_percentage' => round($accuracy, 2) // ปัดเศษทศนิยม 2 ตำแหน่ง
        ];
    }
}

echo json_encode($stats_arr, JSON_UNESCAPED_UNICODE);
$conn->close();
?>