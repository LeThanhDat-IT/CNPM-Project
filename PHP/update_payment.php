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

// Cập nhật trạng thái thanh toán
$stmt = $conn->prepare('UPDATE bookings SET TrangThaiThanhToan = 1 WHERE UPPER(bookingCode) = ?');
$stmt->bind_param('s', $bookingCode);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không cập nhật được trạng thái thanh toán!']);
}
$stmt->close();
$conn->close();
exit;