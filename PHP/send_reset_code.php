<?php

require_once 'connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email) {
        die('Vui lòng nhập email.');
    }

    // Kiểm tra email có tồn tại không
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        // Tạo mã xác nhận ngẫu nhiên
        $reset_code = rand(100000, 999999);

        // Lưu mã vào database
        $update = $conn->prepare("UPDATE users SET reset_code = ? WHERE email = ?");
        $update->bind_param("ss", $reset_code, $email);
        $update->execute();
        $update->close();

        // Gửi email
        $subject = "Mã xác nhận đặt lại mật khẩu";
        $message = "Mã xác nhận của bạn là: $reset_code";
        $headers = "From: no-reply@yourdomain.com\r\n";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'thanhdatle.it@gmail.com'; // Thay bằng email của bạn
            $mail->Password = 'jlup cwie mmje ujde';    // Thay bằng App Password (không phải mật khẩu Gmail)
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('thanhdatle.it@gmail.com', 'Tên hệ thống');
            $mail->addAddress($email);

            $mail->Subject = $subject;
            $mail->Body    = $message;

            if ($mail->send()) {
                echo "Đã gửi mã xác nhận tới email của bạn. Vui lòng kiểm tra hộp thư.";
            } else {
                echo "Không gửi được email. Vui lòng thử lại sau.";
            }
        } catch (Exception $e) {
            echo "Không gửi được email. Lỗi: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email không tồn tại trong hệ thống.";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "Phương thức không hợp lệ.";
}