<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");

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

// ✅ FIX: เปลี่ยน SELECT media_id เป็น id และ FROM Media เป็น media
$sql = "SELECT id, title, description, benefits, risks, level, file_name, file_path, file_type FROM media ORDER BY id";
$result = $conn->query($sql);

$media_arr = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        array_push($media_arr, $row);
    }
}

echo json_encode($media_arr, JSON_UNESCAPED_UNICODE);
$conn->close();
?>