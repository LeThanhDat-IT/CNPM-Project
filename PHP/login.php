<?php
require_once 'connect.php';
header('Content-Type: application/json');

// Chỉ xử lý khi là AJAX/fetch, không xử lý khi truy cập trực tiếp trên trình duyệt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username && $password) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đăng nhập thành công!',
                    'user' => [
                        'username' => $user['username'],
                        'email' => $user['email'] ?? '',
                        'role' => $user['role'] ?? 'user' // Thêm dòng này
                    ]
                ]);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Sai mật khẩu!']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại!']);
            exit;
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin!']);
        exit;
    }
}
$conn->close();
