<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Headers สำหรับการรับคำขอจาก Client
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) { 
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit(); 
}
mysqli_set_charset($conn, "utf8mb4");
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->username) && !empty($data->password)) {
    $username = $data->username;
    $password = $data->password;

    // ตรวจสอบชื่อผู้ใช้จากฐานข้อมูล
    $sql = "SELECT id, username, password, role, is_blocked FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $password_hashed = password_hash("kao12345", PASSWORD_DEFAULT);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $password_hashed = $row['password'];
        $is_blocked = $row['is_blocked'];

        // ตรวจสอบสถานะการบล็อกของผู้ใช้
        if ($is_blocked == 1) {
            http_response_code(403);  // Forbidden
            echo json_encode(["status" => "error", "message" => "บัญชีของคุณถูกบล็อก"]);
            exit();
        }

        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $password_hashed)) {
            // เข้าสู่ระบบสำเร็จ
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_role'] = $row['role'];
            $_SESSION['login_time'] = time(); 

            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "เข้าสู่ระบบสำเร็จ",
                "user" => ["id" => $row['id'], "username" => $row['username'], "role" => $row['role']]
            ], JSON_UNESCAPED_UNICODE);
        } else {
            // รหัสผ่านไม่ถูกต้อง
            http_response_code(401);  // Unauthorized
            echo json_encode(["status" => "error", "message" => "รหัสผ่านไม่ถูกต้อง"], JSON_UNESCAPED_UNICODE);
        }
    } else {
        // ไม่พบชื่อผู้ใช้ในระบบ
        http_response_code(404);  // Not Found
        echo json_encode(["status" => "error", "message" => "ไม่พบชื่อผู้ใช้นี้ในระบบ"], JSON_UNESCAPED_UNICODE);
    }

    $stmt->close();
} else {
    // ข้อมูลไม่ครบถ้วน
    http_response_code(400);  // Bad Request
    echo json_encode(["status" => "error", "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"], JSON_UNESCAPED_UNICODE);
}

$conn->close();

?>
