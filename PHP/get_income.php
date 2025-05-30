<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');

// Lấy filter từ request
$type = $_GET['type'] ?? 'Theo ngày';
$date = $_GET['date'] ?? date('Y-m-d');

// Truy vấn cơ bản: chỉ lấy các booking đã thanh toán (TrangThaiThanhToan = 1)
$where = "WHERE TrangThaiThanhToan = 1";
$params = [];
$types = "";

if ($type === "Theo ngày") {
    $where .= " AND DATE(checkin) = ?";
    $params[] = $date;
    $types .= "s";
} elseif ($type === "Theo tháng") {
    $where .= " AND YEAR(checkin) = ? AND MONTH(checkin) = ?";
    $params[] = date('Y', strtotime($date));
    $params[] = date('m', strtotime($date));
    $types .= "ss";
} elseif ($type === "Theo quý") {
    $year = date('Y', strtotime($date));
    $month = date('m', strtotime($date));
    $quarter = ceil($month / 3);
    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        if (ceil($i / 3) == $quarter) $months[] = $i;
    }
    $where .= " AND YEAR(checkin) = ? AND MONTH(checkin) IN (" . implode(',', array_fill(0, count($months), '?')) . ")";
    $params[] = $year;
    foreach ($months as $m) $params[] = $m;
    $types .= "s" . str_repeat("s", count($months));
} elseif ($type === "Theo năm") {
    $where .= " AND YEAR(checkin) = ?";
    $params[] = date('Y', strtotime($date));
    $types .= "s";
}

$sql = "SELECT 
            b.room AS roomId, 
            q.tenPhong AS roomName, 
            b.fullname AS customerName, 
            b.checkin AS date, 
            b.total AS total
        FROM bookings b
        LEFT JOIN quanlyphong q ON b.room = q.maPhong
        $where
        ORDER BY b.checkin DESC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $row['total'] = (int)$row['total'];
    $data[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode($data, JSON_UNESCAPED_UNICODE);