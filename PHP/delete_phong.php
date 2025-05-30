<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';

if (!$name) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tên phòng']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM QuanLyPhong WHERE tenPhong = ?");
$stmt->bind_param("s", $name);
$ok = $stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => $ok]);