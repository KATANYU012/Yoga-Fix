<?php
session_start();

// 📌 ปิดการแสดง error บนจอ และเขียนลง log แทน
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/upload_error.log');

header("Access-Control-Allow-Origin: http://localhost");
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
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "เชื่อมต่อฐานข้อมูลไม่สำเร็จ"]);
    exit();
}
mysqli_set_charset($conn, "utf8mb4");

$response = [];

// CHANGED: เพิ่มการตรวจสอบ 'benefits' และ 'risks'
if (isset($_POST['title'], $_POST['description'], $_POST['level'], $_POST['benefits'], $_POST['risks'], $_FILES['mediaFile'])) {
    if ($_FILES['mediaFile']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES["mediaFile"]["name"], PATHINFO_EXTENSION));
        $unique_file_name = "media_" . uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $unique_file_name;
        $file_type = $_FILES['mediaFile']['type'];

        if (move_uploaded_file($_FILES["mediaFile"]["tmp_name"], $target_file)) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $level = $_POST['level'];
            // ADDED: รับค่า benefits และ risks จากฟอร์ม
            $benefits = $_POST['benefits'];
            $risks = $_POST['risks'];

            // CHANGED: อัปเดตคำสั่ง SQL ให้ตรงกับตารางและคอลัมน์ใหม่
            $sql = "INSERT INTO media (title, description, benefits, risks, file_name, file_path, file_type, level) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                http_response_code(500);
                echo json_encode(["message" => "เตรียมคำสั่ง SQL ไม่สำเร็จ"]);
                exit();
            }

            // CHANGED: อัปเดต bind_param ให้รองรับ 8 ตัวแปร (ssssssss)
            $stmt->bind_param("ssssssss", $title, $description, $benefits, $risks, $unique_file_name, $target_file, $file_type, $level);

            if ($stmt->execute()) {
                http_response_code(201);
                $response["message"] = "อัปโหลดไฟล์และบันทึกข้อมูลสำเร็จ";
            } else {
                http_response_code(500);
                $response["message"] = "บันทึกข้อมูลลงฐานข้อมูลไม่สำเร็จ";
            }

            $stmt->close();
        } else {
            http_response_code(500);
            $response["message"] = "ย้ายไฟล์ไปยังโฟลเดอร์ล้มเหลว";
        }
    } else {
        http_response_code(400);
        $response["message"] = "เกิดข้อผิดพลาดระหว่างการอัปโหลดไฟล์";
    }
} else {
    http_response_code(400);
    $response["message"] = "กรุณากรอกข้อมูลให้ครบถ้วน";
}

$conn->close();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>

