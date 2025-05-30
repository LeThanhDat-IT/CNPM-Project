<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$bookingCode = $data['bookingCode'] ?? '';

if (!$bookingCode) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã đặt phòng!']);
    exit;
}

// Lấy mã phòng từ booking
$stmt = $conn->prepare("SELECT room FROM bookings WHERE bookingCode = ?");
$stmt->bind_param("s", $bookingCode);
$stmt->execute();
$stmt->bind_result($room);
$stmt->fetch();
$stmt->close();

if ($room) {
    // Xóa booking
    $stmt = $conn->prepare("DELETE FROM bookings WHERE bookingCode = ?");
    $stmt->bind_param("s", $bookingCode);
    $ok = $stmt->execute();
    $stmt->close();

    // Cập nhật trạng thái phòng về "Còn trống"
    $stmt2 = $conn->prepare("UPDATE quanlyphong SET tinhTrang = 1 WHERE maPhong = ?");
    $stmt2->bind_param("s", $room);
    $stmt2->execute();
    $stmt2->close();

    echo json_encode(['success' => $ok]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy booking!']);
}
$conn->close();