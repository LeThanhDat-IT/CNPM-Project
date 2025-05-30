<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');
$data = json_decode(file_get_contents('php://input'), true);
file_put_contents('log.txt', print_r($data, true));

$maKH = $data['maKH'] ?? '';

if (!$maKH) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã khách hàng!']);
    exit;
}
$sql = "DELETE FROM QuanLyKhachhang WHERE maKH = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $maKH);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Xóa khách hàng thành công!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa: ' . $stmt->error]);
}
$conn->close();