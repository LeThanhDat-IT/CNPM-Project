<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "cnpm_db";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
// Đã bỏ echo "Kết nối thành công!" để tránh lỗi khi gọi API
?>
