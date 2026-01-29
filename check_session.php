<?php
session_start();

// --- ส่วนตรวจสอบ Session Timeout ---
// 3600 วินาที = 1 ชั่วโมง
$timeout_duration = 3600; 

// ตรวจสอบว่ามี session 'login_time' อยู่หรือไม่
if (isset($_SESSION['login_time'])) {
    // คำนวณเวลาที่ผ่านไปตั้งแต่ Login ครั้งล่าสุด
    $elapsed_time = time() - $_SESSION['login_time'];

    // ถ้าเวลาผ่านไปเกินกว่าที่กำหนด (1 ชั่วโมง)
    if ($elapsed_time > $timeout_duration) {
        // ทำลาย session ทั้งหมด (เหมือนการ Logout)
        session_unset();
        session_destroy();
    }
}
// --- จบส่วนตรวจสอบ Session Timeout ---


// --- ส่วน Headers และการตอบกลับ ---
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ตรวจสอบสถานะ Login (ซึ่งอาจจะเพิ่งถูกทำลายไปจากโค้ดด้านบน)
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    http_response_code(200);
    echo json_encode([
        "isLoggedIn" => true,
        "user" => [
            "id" => $_SESSION['user_id'],
            "username" => $_SESSION['username'],
            "role" => $_SESSION['user_role']
        ],
        "login_time" => $_SESSION['login_time'] // ส่งเวลาที่ login กลับไปด้วย
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(200);
    echo json_encode(["isLoggedIn" => false]);
}
?>