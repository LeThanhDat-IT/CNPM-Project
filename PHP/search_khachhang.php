<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');

$ten = isset($_GET['ten']) ? trim($_GET['ten']) : '';
$sdt = isset($_GET['sdt']) ? trim($_GET['sdt']) : '';
$email = isset($_GET['email']) ? trim($_GET['email']) : '';

$where = [];
$params = [];
$types = '';

if ($ten !== '') {
    $where[] = 'ten LIKE ?';
    $params[] = '%' . $ten . '%';
    $types .= 's';
}
if ($sdt !== '') {
    $where[] = 'sdt LIKE ?';
    $params[] = '%' . $sdt . '%';
    $types .= 's';
}
if ($email !== '') {
    $where[] = 'email LIKE ?';
    $params[] = '%' . $email . '%';
    $types .= 's';
}

$sql = 'SELECT * FROM quanlykhachhang';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
//add
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$list = [];
while ($row = $result->fetch_assoc()) {
    $list[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode($list);
?>