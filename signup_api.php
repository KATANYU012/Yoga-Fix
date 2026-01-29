<?php
session_start();

// Headers และส่วนจัดการ OPTIONS request (เหมือนเดิม)
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// การเชื่อมต่อฐานข้อมูล (เหมือนเดิม)
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) { /* ... */ exit(); }

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->username) &&
    !empty($data->email) &&
    !empty($data->password) &&
    !empty($data->birthdate) &&
    !empty($data->gender)
) {
    // ... (ส่วนเตรียมข้อมูลเหมือนเดิม) ...
    $username = $conn->real_escape_string($data->username);
    $password_hashed = password_hash($data->password, PASSWORD_DEFAULT);
    // ...

    $sql = "INSERT INTO users (username, email, password, gender, birthdate) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $data->email, $password_hashed, $data->gender, $data->birthdate);

    if ($stmt->execute()) {
        // *** จุดที่เปลี่ยนแปลง ***
        // 1. ดึง ID ของผู้ใช้ใหม่ที่เพิ่งสร้าง
        $new_user_id = $conn->insert_id;

        // 2. สร้าง Session ให้กับผู้ใช้ใหม่นี้ทันที
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['username'] = $username;
        $_SESSION['user_role'] = 'user'; // Default role

        http_response_code(201);
        echo json_encode(["message" => "สมัครสมาชิกและเข้าสู่ระบบสำเร็จ"], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "ไม่สามารถสมัครสมาชิกได้ อาจมีชื่อผู้ใช้หรืออีเมลนี้ในระบบแล้ว"], JSON_UNESCAPED_UNICODE);
        // ... (ส่วนจัดการ error เหมือนเดิม) ...
    }

    $stmt->close();
} else {
     http_response_code(400);
    echo json_encode(["message" => "ข้อมูลไม่ครบถ้วน"], JSON_UNESCAPED_UNICODE);
    // ... (ส่วนจัดการข้อมูลไม่ครบเหมือนเดิม) ...
}

$conn->close();
?>