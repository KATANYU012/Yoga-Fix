<?php
session_start();
session_unset();
session_destroy();

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");

http_response_code(200);
echo json_encode(["message" => "ออกจากระบบสำเร็จ"]);
?>