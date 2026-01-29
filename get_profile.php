<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Please log in."]);
    exit();
}

// ... (ส่วนเชื่อมต่อฐานข้อมูลเหมือนเดิม) ...
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
mysqli_set_charset($conn, "utf8mb4");

$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, gender, birthdate FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if ($user_data) {
    echo json_encode($user_data, JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(404);
    echo json_encode(["message" => "User not found."]);
}
$stmt->close();
$conn->close();
?>