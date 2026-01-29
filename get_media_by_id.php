<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost");
// ... (Headers อื่นๆ ทั้งหมด)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { http_response_code(403); exit(); }

// ... (ส่วนเชื่อมต่อ DB)
$media_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($media_id > 0) {
    $sql = "SELECT id, title, description, level FROM media WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $media_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($media = $result->fetch_assoc()) {
        echo json_encode($media);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Media not found."]);
    }
    $stmt->close();
}
$conn->close();
?>