<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");
// ... (เพิ่ม Headers อื่นๆ สำหรับ POST, OPTIONS เหมือนไฟล์อื่นๆ)

// ความปลอดภัย: ตรวจสอบว่าเป็นแอดมินหรือไม่
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Access Denied."]);
    exit();
}

// ... (ส่วนเชื่อมต่อ DB เหมือนเดิม) ...
$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
mysqli_set_charset($conn, "utf8mb4");

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id)) {
    $media_id = $data->id;

    // 1. ค้นหาที่อยู่ไฟล์เพื่อลบไฟล์จริงทิ้งก่อน
    $sql_find = "SELECT file_path FROM media WHERE id = ?";
    $stmt_find = $conn->prepare($sql_find);
    $stmt_find->bind_param("i", $media_id);
    $stmt_find->execute();
    $result = $stmt_find->get_result();
    if ($row = $result->fetch_assoc()) {
        $file_to_delete = $row['file_path'];
        // ตรวจสอบว่ามีไฟล์อยู่จริงหรือไม่ แล้วทำการลบ
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
    }
    $stmt_find->close();

    // 2. ลบข้อมูลออกจากฐานข้อมูล
    $sql_delete = "DELETE FROM media WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $media_id);

    if ($stmt_delete->execute()) {
        http_response_code(200);
        echo json_encode(["message" => "ลบข้อมูลสำเร็จ"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "เกิดข้อผิดพลาดในการลบข้อมูล"]);
    }
    $stmt_delete->close();
}
$conn->close();
?>