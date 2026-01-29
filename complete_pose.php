session_start();

// 1. เช็คก่อนว่าใครล็อกอินอยู่ โดยดูจาก Session
if(isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];
    $pose_name_from_frontend = $_POST['pose']; // รับชื่อท่ามาจาก Frontend

    // 2. สร้างคำสั่ง SQL เพื่อบันทึกข้อมูล
    // โดยระบุ user_id ของคนที่ล็อกอินอยู่
    $sql = "INSERT INTO user_progress (user_id, pose_name) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $current_user_id, $pose_name_from_frontend);
    $stmt->execute();

    echo "บันทึกความสำเร็จเรียบร้อย!";
}