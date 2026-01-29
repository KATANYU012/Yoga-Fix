<?php  
session_start();
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=utf-8");  // ตั้งค่าให้เป็น JSON

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) { 
    http_response_code(401); 
    echo json_encode(["message" => "กรุณาล็อกอินก่อน"]);
    exit(); 
}

// เชื่อมต่อฐานข้อมูล
require_once 'db_connection.php'; // แก้ไขให้มีไฟล์เชื่อมต่อฐานข้อมูลจริง
if (!$conn) {
    http_response_code(500);
    echo json_encode(["message" => "ไม่สามารถเชื่อมต่อฐานข้อมูล"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"));

// ตรวจสอบข้อมูลที่ได้รับ
if (empty($data->new_password) || empty($data->confirm_password) || empty($data->current_password)) {
    http_response_code(400);
    echo json_encode(["message" => "กรุณากรอกรหัสผ่านให้ครบถ้วน"]);
    exit();
}

// 1. ตรวจสอบรหัสผ่านใหม่กับรหัสผ่านยืนยัน
if ($data->new_password !== $data->confirm_password) {
    echo json_encode(["message" => "รหัสผ่านใหม่ไม่ตรงกัน"]); 
    exit();
}

// 2. ตรวจสอบความยาวของรหัสผ่านใหม่
if (strlen($data->new_password) < 8) {
    echo json_encode(["message" => "รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 8 ตัว"]);
    exit();
}

// 3. ดึงรหัสผ่านปัจจุบันจาก DB
$sql = "SELECT password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["message" => "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL"]);
    exit();
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 4. ตรวจสอบรหัสผ่านปัจจุบัน
if (!password_verify($data->current_password, $user['password'])) {
    echo json_encode(["message" => "รหัสผ่านปัจจุบันไม่ถูกต้อง"]);
    exit();
}

// 5. ถ้ารหัสผ่านปัจจุบันถูกต้อง ให้ทำการเปลี่ยนรหัสผ่านใหม่
$new_password_hashed = password_hash($data->new_password, PASSWORD_DEFAULT);

// 6. ทำการอัปเดตรหัสผ่านใหม่ในฐานข้อมูล
$update_sql = "UPDATE users SET password = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
if (!$update_stmt) {
    echo json_encode(["message" => "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL สำหรับการอัปเดต"]);
    exit();
}
$update_stmt->bind_param("si", $new_password_hashed, $user_id);

if ($update_stmt->execute()) {
    echo json_encode(["message" => "เปลี่ยนรหัสผ่านสำเร็จ"]);
} else {
    echo json_encode(["message" => "เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน"]);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
