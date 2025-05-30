<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT maKH, ten, gioiTinh, ngaySinh, sdt, email, diaChi FROM QuanLyKhachhang";
$result = $conn->query($sql);

$list = [];
while ($row = $result->fetch_assoc()) {
    $list[] = $row;
}
echo json_encode($list);
$conn->close();