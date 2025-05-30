<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';

if (!$username) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tên đăng nhập']);
    exit;
}

// Kiểm tra role của tài khoản
$stmt = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if ($role === 'admin') {
    echo json_encode(['success' => false, 'message' => 'Không thể xóa tài khoản admin!']);
    $conn->close();
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$ok = $stmt->execute();
$stmt->close();
$conn->close();

echo json_encode([
    'success' => $ok,
    'message' => $ok ? 'Xóa tài khoản thành công!' : 'Xóa tài khoản thất bại!'
]);