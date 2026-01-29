<?php
session_start(); // 1. ต้องเริ่ม Session ก่อนเสมอ

// 2. Headers ที่จำเป็น (รวมถึงการอนุญาตให้ส่ง Cookie)
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(); }

// 3. ตรวจสอบสิทธิ์ (สำคัญที่สุด!)
// ต้องเป็น Admin เท่านั้นถึงจะบล็อกคนอื่นได้
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(["message" => "Access Denied."]);
    exit();
}

// 4. เชื่อมต่อฐานข้อมูล (ตามมาตรฐานของเรา)
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

// 5. รับข้อมูล (เหมือนของคุณ)
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['user_id'])) {
    http_response_code(400);
    echo json_encode(["message" => "กรุณาระบุ user_id"]);
    exit();
}

$user_id = $data['user_id'];

// 6. ดึงสถานะปัจจุบัน (ตรรกะของคุณ)
$query = "SELECT is_blocked FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["message" => "ไม่พบผู้ใช้ในระบบ"]);
    $stmt->close();
    $conn->close();
    exit();
}

$row = $result->fetch_assoc();
$is_blocked = $row['is_blocked'];
$stmt->close();

// 7. สลับสถานะ (ตรรกะของคุณ)
$new_status = ($is_blocked == 1) ? 0 : 1;

// 8. อัปเดตสถานะ (ตรรกะของคุณ)
$update_sql = "UPDATE users SET is_blocked = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("ii", $new_status, $user_id);

if ($update_stmt->execute()) {
    $message = $new_status == 1 ? "บัญชีของผู้ใช้ถูกบล็อกแล้ว" : "บัญชีของผู้ใช้ถูกปลดบล็อก";
    echo json_encode(["message" => $message], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode(["message" => "เกิดข้อผิดพลาดในการอัปเดตสถานะ"], JSON_UNESCAPED_UNICODE);
}

$update_stmt->close();
$conn->close();
?>