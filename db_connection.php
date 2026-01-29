<?php
// db_connection.php
$host = '127.0.0.1';    // ที่อยู่ฐานข้อมูล (localhost)
$user = 'root';          // ชื่อผู้ใช้ฐานข้อมูล
$password = '';          // รหัสผ่านฐานข้อมูล (ถ้าใช้ XAMPP ปกติจะเป็น '' หรือ 'root')
$db = 'smartyoga_db';    // ชื่อฐานข้อมูลที่ต้องการเชื่อมต่อ

// สร้างการเชื่อมต่อ
$conn = new mysqli($host, $user, $password, $db);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
