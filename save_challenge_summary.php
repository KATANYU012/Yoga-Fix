<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

// ความปลอดภัย: ตรวจสอบว่าล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Please log in to save summary."]);
    exit();
}

$servername = "localhost"; 
$username_db = "root"; 
$password_db = ""; 
$dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
mysqli_set_charset($conn, "utf8mb4");

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"));

// ตรวจสอบว่าได้รับข้อมูลครบถ้วน
if (isset($data->level) && isset($data->posesPassed) && isset($data->totalPoses)) {
    
    $sql = "INSERT INTO challenge_summary (user_id, challenge_level, poses_passed, total_poses) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // 'isii' หมายถึง integer, string, integer, integer
    $stmt->bind_param("isii", $user_id, $data->level, $data->posesPassed, $data->totalPoses);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "บันทึกผลสรุปสำเร็จ"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "เกิดข้อผิดพลาดในการบันทึกผลสรุป"]);
    }
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["message" => "ข้อมูลสำหรับบันทึกผลสรุปไม่ครบถ้วน"]);
}
$conn->close();
?>