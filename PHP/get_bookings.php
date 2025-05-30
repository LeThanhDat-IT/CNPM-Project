<?php
header('Content-Type: application/json');
require_once 'connect.php';

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
if ($email === '') {
    // Lấy tất cả booking (cho admin)
    $sql = "SELECT * FROM bookings ORDER BY id DESC";
    $result = $conn->query($sql);
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    $conn->close();
    echo json_encode(['success' => true, 'bookings' => $bookings]);
    exit;
}

// Nếu có email thì chỉ lấy booking của email đó
$stmt = $conn->prepare("SELECT * FROM bookings WHERE email = ? ORDER BY id DESC");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'bookings' => $bookings]);
