<?php
// ตั้งค่า Headers เพื่ออนุญาตการเชื่อมต่อและกำหนดประเภทข้อมูล
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");

// ข้อมูลสำหรับเชื่อมต่อฐานข้อมูล
$servername = "localhost"; 
$username_db = "root"; 
$password_db = ""; 
$dbname = "smartyoga_db";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// ตั้งค่าการเข้ารหัสตัวอักษรเป็น utf8mb4 เพื่อรองรับภาษาไทย
mysqli_set_charset($conn, "utf8mb4");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    // หากเชื่อมต่อไม่ได้ ให้ส่ง http status 500 และ array ว่างกลับไป
    http_response_code(500);
    echo json_encode([]);
    exit();
}

// คำสั่ง SQL สำหรับดึงข้อมูลท่าโยคะระดับ 'Basic'
$sql = "SELECT title, file_path FROM media WHERE level = 'Basic' ORDER BY id";
$result = $conn->query($sql);

$poses_arr = [];
if ($result && $result->num_rows > 0) {
    // วนลูปเพื่อนำข้อมูลแต่ละแถวมาใส่ใน array
    while($row = $result->fetch_assoc()) {
        $poses_arr[] = $row;
    }
}

// แปลง array เป็น JSON แล้วส่งข้อมูลกลับไป
echo json_encode($poses_arr, JSON_UNESCAPED_UNICODE);

// ปิดการเชื่อมต่อ
$conn->close();
?>