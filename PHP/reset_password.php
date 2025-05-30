<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$code || !$password || !$confirm_password) {
        die('Vui lòng nhập đầy đủ thông tin.');
    }
    if ($password !== $confirm_password) {
        die('Mật khẩu nhập lại không khớp.');
    }
    if (strlen($password) < 6) {
        die('Mật khẩu phải có ít nhất 6 ký tự.');
    }

    // Tìm user theo mã xác nhận
    $stmt = $conn->prepare("SELECT email FROM users WHERE reset_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($email);
        $stmt->fetch();
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $update = $conn->prepare("UPDATE users SET password = ?, reset_code = NULL WHERE reset_code = ?");
        $update->bind_param("ss", $hashed, $code);
        if ($update->execute()) {
            echo "Đặt lại mật khẩu thành công! <a href='../dangnhap.html'>Đăng nhập</a>";
        } else {
            echo "Có lỗi xảy ra khi cập nhật mật khẩu.";
        }
        $update->close();
    } else {
        echo "Mã xác nhận không đúng hoặc đã hết hạn.";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "Phương thức không hợp lệ.";
}
?>