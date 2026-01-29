<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(); }

// ความปลอดภัย: ตรวจสอบว่าเป็นแอดมินหรือไม่
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(["message" => "Access Denied."]);
    exit();
}

// ... (ส่วนเชื่อมต่อ DB เหมือนเดิม) ...
$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
mysqli_set_charset($conn, "utf8mb4");
if ($conn->connect_error) { exit(); }

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->title) && !empty($data->description) && !empty($data->level)) {
    // ใช้ค่า placeholder สำหรับไฟล์ไปก่อน เพราะยังไม่มีระบบอัปโหลด
    $file_name = "placeholder.jpg";
    $file_path = "/images/placeholder.jpg";
    $file_type = "image";

    $sql = "INSERT INTO media (title, description, file_name, file_path, file_type, level) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $data->title, $data->description, $file_name, $file_path, $file_type, $data->level);

    if ($stmt->execute()) {
        http_response_code(201); // Created
        echo json_encode(["message" => "เพิ่มท่าโยคะสำเร็จ"], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "เกิดข้อผิดพลาดในการบันทึกข้อมูล"], JSON_UNESCAPED_UNICODE);
    }
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["message" => "กรุณากรอกข้อมูลให้ครบทุกช่อง"], JSON_UNESCAPED_UNICODE);
}
$conn->close();
?>