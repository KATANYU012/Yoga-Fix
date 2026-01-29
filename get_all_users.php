<?php
session_start();
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Access Denied."]);
    exit();
}

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
mysqli_set_charset($conn, "utf8mb4");

if ($conn->connect_error) { 
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed."]);
    exit();
}

$sql = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

$users_arr = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        array_push($users_arr, $row);
    }
}
echo json_encode($users_arr, JSON_UNESCAPED_UNICODE);
$conn->close();
?>