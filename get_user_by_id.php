<?php
session_start();
// ... (Headers และ Security Check เหมือนไฟล์อื่นๆ) ...

// ... (ส่วนเชื่อมต่อ DB)
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id > 0) {
    $sql = "SELECT id, username, email, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        echo json_encode($user);
    } else {
        http_response_code(404);
    }
    $stmt->close();
}
$conn->close();
?>