<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $dob = isset($_POST['dob']) ? trim($_POST['dob']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm-password']) ? trim($_POST['confirm-password']) : '';
    $agree = isset($_POST['agree']);
    $role = $_POST['role'] ?? 'user'; // Mặc định là user nếu không có

    if ($name && $phone && $email && $gender && $dob && $address && $username && $password && $confirm_password && $agree) {
        if ($password !== $confirm_password) {
            echo "<script>alert('Mật khẩu và xác nhận mật khẩu không khớp!'); window.history.back();</script>";
        } else {
            // Kiểm tra trùng email, số điện thoại, tên đăng nhập
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ? OR username = ?");
            $stmt->bind_param('sss', $email, $phone, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($row['email'] === $email) {
                    echo "<script>alert('Email đã được sử dụng!'); window.history.back();</script>";
                } elseif ($row['phone'] === $phone) {
                    echo "<script>alert('Số điện thoại đã được sử dụng!'); window.history.back();</script>";
                } elseif ($row['username'] === $username) {
                    echo "<script>alert('Tên đăng nhập đã được sử dụng!'); window.history.back();</script>";
                } else {
                    echo "<script>alert('Thông tin đã được sử dụng!'); window.history.back();</script>";
                }
                $stmt->close();
            } else {
                $stmt->close();
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt2 = $conn->prepare("INSERT INTO users (name, phone, email, gender, dob, address, username, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt2->bind_param('sssssssss', $name, $phone, $email, $gender, $dob, $address, $username, $password_hash, $role);
                if ($stmt2->execute()) {
                    echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href='../dangnhap.html';</script>";
                    exit();
                } else {
                    echo "<script>alert('Lỗi: " . addslashes($stmt2->error) . "'); window.history.back();</script>";
                }
                $stmt2->close();
            }
        }
    } else {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.history.back();</script>";
    }
}
$conn->close();
?>