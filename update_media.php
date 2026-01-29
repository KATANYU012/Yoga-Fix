<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost");
// ... (Headers อื่นๆ ทั้งหมด)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { http_response_code(403); exit(); }

// ... (ส่วนเชื่อมต่อ DB)
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id) && !empty($data->title)) {
    $sql = "UPDATE media SET title = ?, description = ?, level = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $data->title, $data->description, $data->level, $data->id);
    if ($stmt->execute()) {
        echo json_encode(["message" => "อัปเดตข้อมูลสำเร็จ"]);
    } else {
        echo json_encode(["message" => "เกิดข้อผิดพลาด"]);
    }
    $stmt->close();
}
$conn->close();
?>