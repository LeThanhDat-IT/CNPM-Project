<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

$maPhong = $data['maPhong'] ?? '';
$name = $data['name'] ?? '';
$type = $data['type'] ?? '';
$price = $data['price'] ?? '';
$status = $data['status'] ?? '';
$hinhAnh = isset($data['hinhAnh']) ? $data['hinhAnh'] : null;
$bedType = isset($data['bedType']) ? $data['bedType'] : null;
$amenities = isset($data['amenities']) ? $data['amenities'] : null;
$area = isset($data['area']) ? $data['area'] : null;
$sucChua = isset($data['sucChua']) ? $data['sucChua'] : null;

if (!$maPhong) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã phòng!']);
    exit;
}

// Đảm bảo truyền giá trị null nếu chuỗi rỗng
$hinhAnh = $hinhAnh === '' ? null : $hinhAnh;
$bedType = $bedType === '' ? null : $bedType;
$amenities = $amenities === '' ? null : $amenities;
$area = $area === '' ? null : $area;
$sucChua = $sucChua === '' ? null : $sucChua;

$sql = "UPDATE quanlyphong SET tenPhong=?, kieuPhong=?, giaPhong=?, tinhTrang=?, hinhAnh=?, loaiGiuong=?, tienNghi=?, dienTich=?, sucChua=? WHERE maPhong=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssisssssis",
    $name,      // tenPhong (string)
    $type,      // kieuPhong (string)
    $price,     // giaPhong (int)
    $status,    // tinhTrang (string)
    $hinhAnh,   // hinhAnh (string)
    $bedType,   // loaiGiuong (string)
    $amenities, // tienNghi (string)
    $area,      // dienTich (string)
    $sucChua,   // sucChua (int)
    $maPhong    // maPhong (string)
);

$ok = $stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => $ok]);
?>
