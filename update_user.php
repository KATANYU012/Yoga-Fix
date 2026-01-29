<?php
session_start();
// ... (Headers และ Security Check) ...

// ... (ส่วนเชื่อมต่อ DB) ...
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id) && !empty($data->username) && !empty($data->email) && !empty($data->role)) {
    // (Optional) เพิ่มโค้ดตรวจสอบ username/email ซ้ำกับคนอื่น

    $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $data->username, $data->email, $data->role, $data->id);
    if ($stmt->execute()) {
        echo json_encode(["message" => "อัปเดตข้อมูลสำเร็จ"]);
    } else {
        echo json_encode(["message" => "เกิดข้อผิดพลาด"]);
    }
    $stmt->close();
}
$conn->close();
?>