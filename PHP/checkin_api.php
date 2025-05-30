<?php
header('Content-Type: application/json');
require_once 'connect.php';

// Lấy bookingCode từ POST (form hoặc JSON)
$bookingCode = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nếu là JSON
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        $bookingCode = isset($input['bookingCode']) ? strtoupper(trim($input['bookingCode'])) : '';
    } else {
        // Form thường
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

// Truy vấn database
$stmt = $conn->prepare('SELECT * FROM bookings WHERE UPPER(bookingCode) = ? LIMIT 1');
$stmt->bind_param('s', $bookingCode);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy mã đặt phòng này!']);
    exit;
}

// Đảm bảo trường đúng tên cho JS
if (isset($booking['TrangThaiThanhToan'])) {
    $booking['TrangThaiThanhToan'] = (int)$booking['TrangThaiThanhToan'];
} elseif (isset($booking['trangthaithanhtoan'])) {
    $booking['TrangThaiThanhToan'] = (int)$booking['trangthaithanhtoan'];
}

// Sau khi lấy $booking thành công
// KHÔNG thêm khách hàng ở đây nữa!
// Chỉ trả về thông tin booking

$action = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = isset($input['action']) ? $input['action'] : '';
    } else {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
    }
}

// Nếu là yêu cầu xác nhận check-in
if ($action === 'checkin') {
    // Cập nhật trạng thái phòng đã nhận phòng trong bookings
    $stmtUpdate = $conn->prepare("UPDATE bookings SET TrangThaiPhong = 1 WHERE bookingCode = ?");
    $stmtUpdate->bind_param("s", $bookingCode);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    // Lấy mã phòng từ booking
    $roomCode = $booking['room'];
    // Cập nhật trạng thái phòng trong quanlyphong (ví dụ: 0 = Đã thuê)
    $stmtRoom = $conn->prepare("UPDATE quanlyphong SET tinhTrang = 0 WHERE maPhong = ?");
    $stmtRoom->bind_param("s", $roomCode);
    $stmtRoom->execute();
    $stmtRoom->close();

    // Lấy thông tin khách hàng từ booking
    $email = $booking['email'];
    $fullname = $booking['fullname'];
    $phone = $booking['phone'];
    $gioiTinh = isset($booking['gioiTinh']) ? $booking['gioiTinh'] : '';
    $ngaySinh = isset($booking['ngaySinh']) ? $booking['ngaySinh'] : null;
    $diaChi = isset($booking['diaChi']) ? $booking['diaChi'] : '';

    // Kiểm tra khách hàng đã tồn tại chưa (theo email hoặc sdt)
    $stmtKH = $conn->prepare("SELECT maKH FROM quanlykhachhang WHERE email = ? OR sdt = ?");
    $stmtKH->bind_param("ss", $email, $phone);
    $stmtKH->execute();
    $stmtKH->store_result();

    if ($stmtKH->num_rows === 0) {
        // Sinh mã khách hàng mới
        $sql_max = "SELECT MAX(maKH) AS max_maKH FROM quanlykhachhang";
        $result = $conn->query($sql_max);
        $row = $result->fetch_assoc();
        $max_maKH = $row['max_maKH'];
        if ($max_maKH) {
            $num = intval(substr($max_maKH, 1)) + 1;
            $maKH = 'A' . str_pad($num, 3, '0', STR_PAD_LEFT);
        } else {
            $maKH = 'A001';
        }
        // Thêm khách hàng mới
        $stmtAdd = $conn->prepare("INSERT INTO quanlykhachhang (maKH, ten, sdt, email, gioiTinh, ngaySinh, diaChi) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmtAdd->bind_param("sssssss", $maKH, $fullname, $phone, $email, $gioiTinh, $ngaySinh, $diaChi);
        $stmtAdd->execute();
        $stmtAdd->close();
    }
    $stmtKH->close();

    echo json_encode(['success' => true, 'message' => 'Check-in thành công!']);
    $conn->close();
    exit;
}

// ...phần còn lại: chỉ trả về thông tin booking, KHÔNG thêm khách hàng...
echo json_encode(['success' => true, 'booking' => $booking]);
file_put_contents('debug_checkin.txt', print_r($booking, true));
$stmt->close();
$conn->close();
exit;