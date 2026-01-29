<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");
// ... (เพิ่ม Headers อื่นๆ สำหรับ POST, OPTIONS)

// ความปลอดภัย: ตรวจสอบว่าเป็นแอดมินหรือไม่
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Access Denied."]);
    exit();
}

$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
mysqli_set_charset($conn, "utf8mb4");

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id)) {
    $user_id_to_delete = $data->id;

    // ป้องกันแอดมินลบบัญชีตัวเอง
    if ($user_id_to_delete == $_SESSION['user_id']) {
        http_response_code(400);
        echo json_encode(["message" => "ไม่สามารถลบบัญชีของตัวเองได้"]);
        exit();
    }

    // เริ่ม Transaction เพื่อให้แน่ใจว่าลบข้อมูลครบทุกตาราง
    $conn->begin_transaction();

    try {
        // 1. ลบประวัติการฝึกของผู้ใช้ก่อน
        $sql_progress = "DELETE FROM user_progress WHERE user_id = ?";
        $stmt_progress = $conn->prepare($sql_progress);
        $stmt_progress->bind_param("i", $user_id_to_delete);
        $stmt_progress->execute();
        $stmt_progress->close();

        // 2. ลบผู้ใช้ออกจากตาราง users
        $sql_user = "DELETE FROM users WHERE id = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("i", $user_id_to_delete);
        $stmt_user->execute();
        $stmt_user->close();

        // ถ้าสำเร็จทั้งหมด ให้ commit
        $conn->commit();
        http_response_code(200);
        echo json_encode(["message" => "ลบผู้ใช้สำเร็จ"]);

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback(); // ถ้ามีข้อผิดพลาด ให้ยกเลิกทั้งหมด
        http_response_code(500);
        echo json_encode(["message" => "เกิดข้อผิดพลาดในการลบข้อมูล"]);
    }
}
$conn->close();
?>