<?php
// get_last_booking.php
header('Content-Type: application/json');
require_once 'connect.php';

// Lấy user_id từ session hoặc query string (tùy hệ thống đăng nhập)
// Ở đây demo lấy theo email truyền qua GET/POST
$email = isset($_GET['email']) ? $_GET['email'] : (isset($_POST['email']) ? $_POST['email'] : '');
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Thiếu email']);
    exit;
}

// Lấy booking mới nhất của user
$sql = "SELECT * FROM bookings WHERE email = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $booking = [
        "room" => $row["room"],
        "roomName" => $row["roomName"], // Thêm dòng này
        "fullname" => $row["fullname"],
        "phone" => $row["phone"],
        "email" => $row["email"],
        "checkin" => $row["checkin"],
        "checkout" => $row["checkout"],
        "bookingCode" => $row["bookingCode"],
        "total" => (int)$row["total"]
    ];
    echo json_encode(["success" => true, "booking" => $booking]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy đặt phòng']);
}
$stmt->close();
$conn->close();
