<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');
$data = json_decode(file_get_contents('php://input'), true);

$ten = $data['ten'] ?? '';
$gioiTinh = $data['gioiTinh'] ?? '';
$ngaySinh = $data['ngaySinh'] ?? '';
$sdt = $data['sdt'] ?? '';
$email = $data['email'] ?? '';
$diaChi = $data['diaChi'] ?? '';

// Sinh mã khách hàng mới tự động
$sql_max = "SELECT MAX(maKH) AS max_maKH FROM QuanLyKhachhang";
$result = $conn->query($sql_max);
$row = $result->fetch_assoc();
$max_maKH = $row['max_maKH'];

if ($max_maKH) {
    $num = intval(substr($max_maKH, 1)) + 1;
    $maKH = 'A' . str_pad($num, 3, '0', STR_PAD_LEFT);
} else {
    $maKH = 'A001';
}

$sql = "INSERT INTO QuanLyKhachhang (maKH, ten, gioiTinh, ngaySinh, sdt, email, diaChi)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $maKH, $ten, $gioiTinh, $ngaySinh, $sdt, $email, $diaChi);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Thêm khách hàng thành công!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm khách hàng: ' . $stmt->error]);
}
$conn->close();