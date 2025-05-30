<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');
$data = json_decode(file_get_contents('php://input'), true);

$maKH = $data['maKH'] ?? '';
$ten = $data['ten'] ?? '';
$gioiTinh = $data['gioiTinh'] ?? '';
$ngaySinh = $data['ngaySinh'] ?? '';
$sdt = $data['sdt'] ?? '';
$email = $data['email'] ?? '';
$diaChi = $data['diaChi'] ?? '';

if (!$maKH) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã khách hàng!']);
    exit;
}

$sql = "UPDATE QuanLyKhachhang SET ten=?, gioiTinh=?, ngaySinh=?, sdt=?, email=?, diaChi=? WHERE maKH=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $ten, $gioiTinh, $ngaySinh, $sdt, $email, $diaChi, $maKH);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cập nhật khách hàng thành công!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật: ' . $stmt->error]);
}
$conn->close();