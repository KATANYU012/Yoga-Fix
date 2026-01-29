<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");

// ตรวจสอบว่าล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Please log in."]);
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
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. ดึงข้อมูลสรุปผล Challenge 5 ครั้งล่าสุด
$sql_summary = "SELECT challenge_level, poses_passed, total_poses, played_at FROM challenge_summary WHERE user_id = ? ORDER BY played_at DESC LIMIT 5";
$stmt_summary = $conn->prepare($sql_summary);
$stmt_summary->bind_param("i", $user_id);
$stmt_summary->execute();
$result_summary = $stmt_summary->get_result();

$summary_history = [];
while($row = $result_summary->fetch_assoc()) {
    $summary_history[] = $row;
}
$stmt_summary->close();

// 2. ดึงประวัติการฝึกท่า 10 ท่าล่าสุด
$sql_progress = "SELECT pose_name, result, completed_at FROM user_progress WHERE user_id = ? ORDER BY completed_at DESC LIMIT 10";
$stmt_progress = $conn->prepare($sql_progress);
$stmt_progress->bind_param("i", $user_id);
$stmt_progress->execute();
$result_progress = $stmt_progress->get_result();

$progress_history = [];
while($row = $result_progress->fetch_assoc()) {
    $progress_history[] = $row;
}
$stmt_progress->close();

$conn->close();

// ส่งข้อมูลทั้งสองส่วนกลับไปเป็น JSON
echo json_encode([
    "challenge_summary" => $summary_history,
    "pose_progress" => $progress_history
], JSON_UNESCAPED_UNICODE);
?>