<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(); }

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Please log in."]);
    exit();
}

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
mysqli_set_charset($conn, "utf8mb4");
if ($conn->connect_error) { exit(); }

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->username) && !empty($data->email) && !empty($data->birthdate)) {
    // ตรวจสอบว่า username ใหม่ซ้ำกับของคนอื่นหรือไม่
    $check_sql = "SELECT id FROM users WHERE username = ? AND id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $data->username, $user_id);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode(["message" => "ชื่อผู้ใช้นี้มีคนอื่นใช้แล้ว"], JSON_UNESCAPED_UNICODE);
        exit();
    }
    $check_stmt->close();

    // อัปเดตข้อมูล
    $sql = "UPDATE users SET username = ?, email = ?, birthdate = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $data->username, $data->email, $data->birthdate, $user_id);

    if ($stmt->execute()) {
        $_SESSION['username'] = $data->username; // อัปเดต session ด้วย
        http_response_code(200);
        echo json_encode(["message" => "อัปเดตโปรไฟล์สำเร็จ"], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "ไม่สามารถอัปเดตโปรไฟล์ได้"], JSON_UNESCAPED_UNICODE);
    }
    $stmt->close();
}
$conn->close();
?>