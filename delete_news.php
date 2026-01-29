<?php
session_start();
// ... (Headers และ Security Check ว่าเป็น Admin เหมือนไฟล์อื่นๆ) ...

$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id)) {
    $sql = "DELETE FROM news WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $data->id);
    if ($stmt->execute()) {
        echo json_encode(["message" => "ลบข่าวสำเร็จ"]);
    } else {
        echo json_encode(["message" => "เกิดข้อผิดพลาด"]);
    }
    $stmt->close();
}
$conn->close();
?>