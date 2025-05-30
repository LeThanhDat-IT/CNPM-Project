<?php
header('Content-Type: application/json');
require_once 'connect.php';

$bookingCode = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        $bookingCode = isset($input['bookingCode']) ? strtoupper(trim($input['bookingCode'])) : '';
    } else {
        $bookingCode = isset($_POST['bookingCode']) ? strtoupper(trim($_POST['bookingCode'])) : '';
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!$bookingCode) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã đặt phòng']);
    exit;
}

// Kiểm tra tồn tại booking
$stmt = $conn->prepare('SELECT TrangThaiPhong FROM bookings WHERE UPPER(bookingCode) = ? LIMIT 1');
$stmt->bind_param('s', $bookingCode);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy mã đặt phòng!']);
    $conn->close();
    exit;
}
// Kiểm tra nếu đã check-out (TrangThaiPhong = 2)
if (isset($booking['TrangThaiPhong']) && $booking['TrangThaiPhong'] == 2) {
    echo json_encode(['success' => false, 'message' => 'Phòng này đã được trả trước đó!']);
    $conn->close();
    exit;
}

// Cập nhật trạng thái trả phòng
$stmt = $conn->prepare('UPDATE bookings SET TrangThaiPhong = 2 WHERE UPPER(bookingCode) = ?');
$stmt->bind_param('s', $bookingCode);
$stmt->execute();

// Sau khi cập nhật trạng thái trả phòng thành công
if ($stmt->affected_rows > 0) {
    // Lấy mã phòng từ booking
    $stmtRoom = $conn->prepare('SELECT room FROM bookings WHERE UPPER(bookingCode) = ? LIMIT 1');
    $stmtRoom->bind_param('s', $bookingCode);
    $stmtRoom->execute();
    $resultRoom = $stmtRoom->get_result();
    if ($rowRoom = $resultRoom->fetch_assoc()) {
        $maPhong = $rowRoom['room'];
        // Cập nhật trạng thái phòng về "Còn trống"
        $stmtUpdate = $conn->prepare("UPDATE quanlyphong SET tinhTrang = '1' WHERE maPhong = ?");
        $stmtUpdate->bind_param("s", $maPhong);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    }
    $stmtRoom->close();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không cập nhật được trạng thái trả phòng!']);
}
$stmt->close();
$conn->close();
exit;