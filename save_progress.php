<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// จัดการ Preflight Request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ความปลอดภัย: ตรวจสอบว่าล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Please log in to save progress."]);
    exit();
}

// ส่วนเชื่อมต่อ DB
$servername = "localhost"; 
$username_db = "root"; 
$password_db = ""; 
$dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
mysqli_set_charset($conn, "utf8mb4");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->pose_name) && !empty($data->result)) {
    $sql = "INSERT INTO user_progress (user_id, pose_name, result) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $data->pose_name, $data->result);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "บันทึกผลสำเร็จ"], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "เกิดข้อผิดพลาดในการบันทึก"], JSON_UNESCAPED_UNICODE);
    }
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["message" => "ข้อมูลที่ส่งมาไม่ครบถ้วน"], JSON_UNESCAPED_UNICODE);
}
$conn->close();
?>