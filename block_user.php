<?php
// เชื่อมต่อฐานข้อมูล
require_once 'db_connection.php';

// รับข้อมูลจาก client (JSON)
$data = json_decode(file_get_contents("php://input"), true);

// ตรวจสอบว่า user_id ถูกส่งมาหรือไม่
if (empty($data['user_id'])) {
    echo json_encode(["message" => "กรุณาระบุ user_id"]);
    exit();
}

$user_id = $data['user_id']; // user_id ที่ต้องการบล็อก

// เช็คว่า user_id ที่ระบุมีอยู่ในระบบหรือไม่
$query = "SELECT id FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

// ถ้าไม่พบผู้ใช้
if ($stmt->num_rows === 0) {
    echo json_encode(["message" => "ไม่พบผู้ใช้ในระบบ"]);
    exit();
}

// อัปเดตสถานะผู้ใช้เป็น 'บล็อก'
$update_sql = "UPDATE users SET is_blocked = 1 WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $user_id);
if ($update_stmt->execute()) {
    echo json_encode(["message" => "ผู้ใช้ถูกบล็อกสำเร็จ"]);
} else {
    echo json_encode(["message" => "เกิดข้อผิดพลาดในการบล็อกผู้ใช้"]);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
