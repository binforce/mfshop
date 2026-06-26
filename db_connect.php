<?php
// Kiểm tra nếu session chưa được bắt đầu thì mới gọi session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db_name = "mfshop";

$conn = new mysqli($host, $user, $pass, $db_name);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>