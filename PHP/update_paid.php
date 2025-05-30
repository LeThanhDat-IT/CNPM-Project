<?php
header('Content-Type: application/json');
require_once 'connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

$bookingCode = trim($_POST['bookingCode'] ?? '');
$payMethod = $_POST['payMethod'] ?? '';

if ($bookingCode === '') {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã đặt phòng!']);
    exit;
}

// Nếu KHÔNG phải thanh toán tại khách sạn thì mới cập nhật đã thanh toán
if ($payMethod !== 'pay_at_hotel') {
    $stmt = $conn->prepare("UPDATE bookings SET TrangThaiThanhToan = 1 WHERE bookingCode = ?");
    $stmt->bind_param('s', $bookingCode);
    $stmt->execute();
} else {
    // Không cập nhật trạng thái thanh toán
    $stmt = $conn->prepare("SELECT 1"); // Dummy query để giữ logic phía dưới không lỗi
    $stmt->execute();
}

if ($stmt->affected_rows > 0 || $payMethod === 'pay_at_hotel') {
    // Lấy thông tin booking để gửi mail
    $stmt2 = $conn->prepare("SELECT room, roomName, fullname, phone, email, checkin, checkout, total FROM bookings WHERE bookingCode = ?");
    $stmt2->bind_param('s', $bookingCode);
    $stmt2->execute();
    $stmt2->bind_result($room, $roomName, $fullname, $phone, $email, $checkin, $checkout, $total);
    if ($stmt2->fetch()) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'thanhdatle.it@gmail.com';
            $mail->Password = 'jlup cwie mmje ujde';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('thanhdatle.it@gmail.com', 'THE COW HOTEL');
            $mail->addAddress($email, $fullname);

            $mail->isHTML(true);
            // Lấy tên phòng từ bookings
            $tenPhong = $roomName ?: $room;

            $mail->Subject = ($payMethod === 'pay_at_hotel')
                ? 'Xác nhận đặt phòng tại THE COW HOTEL'
                : 'Xác nhận thanh toán tại THE COW HOTEL';

            $mail->Body = "
                <div style='font-family:Montserrat,Arial,sans-serif;max-width:540px;margin:auto;background:#fff;border-radius:18px;box-shadow:0 6px 32px #e4a11b22;padding:36px 36px 28px 36px;border:2px solid #e4a11b;'>
                    <div style='text-align:center;margin-bottom:22px'>
                        <img src='http://localhost/CNPM_Project/images/Logo.png' alt='THE COW HOTEL' style='height:60px;margin-bottom:10px;border-radius:12px;box-shadow:0 2px 8px #e4a11b22;'>
                        <h1 style='color:#e4a11b;margin:0;font-size:2.2em;letter-spacing:2px;font-weight:900;text-shadow:0 2px 8px #e4a11b11;'>THE COW HOTEL</h1>
                    </div>
                    <h2 style='color:#223B79;text-align:center;margin-bottom:26px;font-size:1.35em;letter-spacing:1px;text-shadow:0 2px 8px #e4a11b11'>
                        ".($payMethod === 'pay_at_hotel' ? "XÁC NHẬN ĐẶT PHÒNG" : "THANH TOÁN THÀNH CÔNG")."
                    </h2>
                    <div style='font-size:1.13em;margin-bottom:20px;line-height:1.7;color:#222;text-align:center'>
                        <span style='font-size:1.1em'>👋</span> Kính chào <b style='color:#223B79'>$fullname</b>,<br>
                        ".($payMethod === 'pay_at_hotel'
                            ? "Cảm ơn bạn đã đặt phòng tại <b>THE COW HOTEL</b>.<br>Thông tin đặt phòng của bạn:"
                            : "Bạn đã <b style='color:#e4a11b'>thanh toán thành công</b> tại <b>THE COW HOTEL</b>.<br>Thông tin đặt phòng của bạn:")."
                    </div>
                    <table style='width:100%;font-size:1.07em;margin-bottom:22px;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px #e4a11b11;background:#f8fafc'>
                        <tr>
                            <td style='padding:10px 0 10px 14px;color:#223B79;width:44%'><b>Tên phòng:</b></td>
                            <td style='padding:10px 14px 10px 0'><b style='color:#e4a11b;font-size:1.13em;'>$tenPhong</b></td>
                        </tr>
                        <tr>
                            <td style='padding:8px 0 8px 14px;color:#223B79'><b>Mã phòng:</b></td>
                            <td style='padding:8px 14px 8px 0'>$room</td>
                        </tr>
                        <tr>
                            <td style='padding:8px 0 8px 14px;color:#223B79'><b>Mã đặt phòng:</b></td>
                            <td style='padding:8px 14px 8px 0'><span style='color:#e4a11b;font-weight:bold;font-size:1.13em'>$bookingCode</span></td>
                        </tr>
                        <tr>
                            <td style='padding:8px 0 8px 14px;color:#223B79'><b>Nhận phòng:</b></td>
                            <td style='padding:8px 14px 8px 0'>$checkin</td>
                        </tr>
                        <tr>
                            <td style='padding:8px 0 8px 14px;color:#223B79'><b>Trả phòng:</b></td>
                            <td style='padding:8px 14px 8px 0'>$checkout</td>
                        </tr>
                        <tr>
                            <td style='padding:8px 0 8px 14px;color:#223B79'><b>Tổng cộng:</b></td>
                            <td style='padding:8px 14px 8px 0'><b style='color:#e4a11b;font-size:1.13em'>" . number_format($total, 0, ',', '.') . " VND</b></td>
                        </tr>
                        <tr>
                            <td style='padding:8px 0 8px 14px;color:#223B79'><b>Hình thức thanh toán:</b></td>
                            <td style='padding:8px 14px 8px 0'><b>".($payMethod === 'pay_at_hotel' ? "Thanh toán tại khách sạn" : "Chuyển khoản/Thẻ")."</b></td>
                        </tr>
                    </table>
                    <div style='color:#e4a11b;text-align:center;margin:20px 0 10px 0;font-weight:bold;font-size:1.09em'>
                        <span style='font-size:1.2em'>⚠️</span> Vui lòng lưu lại mã đặt phòng để xuất trình khi đến khách sạn check-in.
                    </div>
                    <div style='font-size:13px;color:#888;text-align:center;margin-bottom:10px'>
                        Đây là email tự động, vui lòng không trả lời.<br>
                        Mọi thắc mắc xin liên hệ: <a href='mailto:thecowhotel@gmail.com' style='color:#e4a11b;text-decoration:none'>thecowhotel@gmail.com</a> hoặc hotline <b>0123 456 789</b>.
                    </div>
                    <div style='text-align:center;margin-top:16px;font-size:1.13em;color:#e4a11b;font-weight:bold'>
                        Hẹn gặp lại bạn tại THE COW HOTEL! <span style='font-size:1.1em'>🌿</span>
                    </div>
                </div>
            ";
            $mail->send();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi gửi mail: ' . $mail->ErrorInfo]);
            $stmt2->close();
            $stmt->close();
            $conn->close();
            exit;
        }
    }
    $stmt2->close();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy mã đặt phòng hoặc đã thanh toán!']);
}
$stmt->close();
$conn->close();