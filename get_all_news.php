<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "smartyoga_db";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
mysqli_set_charset($conn, "utf8mb4");

// ดึงข้อมูลข่าวทั้งหมด โดยเชื่อมตาราง news กับ users เพื่อเอาชื่อผู้เขียน (username) มาด้วย
$sql = "SELECT news.id, news.title, news.content, news.created_at, users.username as author_name 
        FROM news 
        JOIN users ON news.author_id = users.id 
        ORDER BY news.created_at DESC";

$result = $conn->query($sql);
$news_arr = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $news_arr[] = $row;
    }
}
echo json_encode($news_arr, JSON_UNESCAPED_UNICODE);
$conn->close();
?>