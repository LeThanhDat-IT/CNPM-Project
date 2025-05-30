<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'connect.php';
require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room = trim($_POST['room'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $note = trim($_POST['note'] ?? '');
    $gioiTinh = $_POST['gioiTinh'] ?? '';
    $ngaySinh = $_POST['ngaySinh'] ?? '';
    $diaChi = $_POST['diaChi'] ?? '';

    // Ghi lại thông tin nhận được từ form vào file log.txt
    file_put_contents('log.txt', print_r($_POST, true), FILE_APPEND);

    // Kiểm tra dữ liệu đầu vào
    if (empty($room) || empty($fullname) || empty($phone) || empty($email) || empty($date) || empty($time)) {
        echo json_encode(['error' => 'Vui lòng điền đầy đủ thông tin.']);
        exit;
    }

    // Bước 1: Chèn bản ghi chưa có bookingCode
    $stmt = $conn->prepare("INSERT INTO bookings (room, fullname, phone, email, gioiTinh, ngaySinh, diaChi, checkin, checkout, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssssss", $room, $fullname, $phone, $email, $gioiTinh, $ngaySinh, $diaChi, $date, $time);

    if ($stmt->execute()) {
        $last_id = $conn->insert_id;
        // Bước 2: Sinh bookingCode duy nhất
        $bookingCode = 'TCH' . date('Ymd') . str_pad($last_id, 3, '0', STR_PAD_LEFT);
        // Bước 3: Update bookingCode cho bản ghi vừa chèn
        $stmt2 = $conn->prepare("UPDATE bookings SET bookingCode = ? WHERE id = ?");
        $stmt2->bind_param("si", $bookingCode, $last_id);
        $stmt2->execute();
        $stmt2->close();

        // Lấy giá phòng từ bảng quanlyphong
        $stmt3 = $conn->prepare("SELECT giaPhong FROM quanlyphong WHERE maPhong = ?");
        $stmt3->bind_param("s", $room); // $room là mã phòng
        $stmt3->execute();
        $stmt3->bind_result($giaPhong);
        $stmt3->fetch();
        $stmt3->close();

        // Tính số ngày ở
        $ngay_vao = new DateTime($date);
        $ngay_ra = new DateTime($time);
        $so_ngay = $ngay_vao->diff($ngay_ra)->days;
        if ($so_ngay == 0) $so_ngay = 1; // ít nhất 1 ngày

        $tong_tien = $giaPhong * $so_ngay;

        // Cập nhật total cho booking vừa tạo
        $stmt4 = $conn->prepare("UPDATE bookings SET total = ? WHERE id = ?");
        $stmt4->bind_param("ii", $tong_tien, $last_id);
        $stmt4->execute();
        $stmt4->close();

        // Sau khi lấy $room và $last_id
        $stmtTenPhong = $conn->prepare("SELECT tenPhong FROM quanlyphong WHERE maPhong = ?");
        $stmtTenPhong->bind_param("s", $room);
        $stmtTenPhong->execute();
        $stmtTenPhong->bind_result($tenPhong);
        $stmtTenPhong->fetch();
        $stmtTenPhong->close();

        $stmtUpdateRoomName = $conn->prepare("UPDATE bookings SET roomName = ? WHERE id = ?");
        $stmtUpdateRoomName->bind_param("si", $tenPhong, $last_id);
        $stmtUpdateRoomName->execute();
        $stmtUpdateRoomName->close();

        // Cập nhật trạng thái phòng thành "đã thuê" (ví dụ: 0 hoặc 2 tùy quy ước)
        $stmtUpdateStatus = $conn->prepare("UPDATE quanlyphong SET tinhTrang = 0 WHERE maPhong = ?");
        $stmtUpdateStatus->bind_param("s", $room);
        $stmtUpdateStatus->execute();
        $stmtUpdateStatus->close();

        // --- Bỏ phần gửi email xác nhận đặt phòng ở đây ---

        echo json_encode(['success' => true, 'message' => 'Đặt phòng thành công!', 'bookingCode' => $bookingCode]);
    } else {
        echo json_encode(['error' => 'Đã xảy ra lỗi. Vui lòng thử lại sau.']);
    }

    $stmt->close();
}

$conn->close();
