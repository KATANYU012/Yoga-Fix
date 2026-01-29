<?php
session_start();
// ... (Headers และ Security Check ว่าเป็น Admin เหมือนไฟล์อื่นๆ) ...

$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
mysqli_set_charset($conn, "utf8mb4");

$data = json_decode(file_get_contents("php://input"));
$author_id = $_SESSION['user_id']; // ดึง ID ของแอดมินที่ล็อกอินอยู่

if (!empty($data->title) && !empty($data->content)) {
    $sql = "INSERT INTO news (title, content, author_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $data->title, $data->content, $author_id);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "เพิ่มข่าวสำเร็จ"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "เกิดข้อผิดพลาด"]);
    }
    $stmt->close();
}
$conn->close();
?>