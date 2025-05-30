<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');

// Đồng bộ trạng thái phòng: nếu không còn booking nào đang thuê thì chuyển về "Còn trống"
$conn->query("
    UPDATE quanlyphong q
    LEFT JOIN (
        SELECT room FROM bookings WHERE TrangThaiThanhToan = 1
    ) b ON q.maPhong = b.room
    SET q.tinhTrang = 1
    WHERE b.room IS NULL AND q.tinhTrang = 0
");

$sql = "SELECT maPhong, tenPhong, kieuPhong, giaPhong, tinhTrang, hinhAnh, loaiGiuong, tienNghi, dienTich, sucChua FROM quanlyphong ORDER BY maPhong ASC";
$result = $conn->query($sql);

$phong = array();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $phong[] = [
            'maPhong' => $row['maPhong'],
            'tenPhong' => $row['tenPhong'],
            'giaPhong' => $row['giaPhong'],
            'kieuPhong' => $row['kieuPhong'],
            'tinhTrang' => $row['tinhTrang'],
            'hinhAnh'  => $row['hinhAnh'],
            'loaiGiuong' => $row['loaiGiuong'],
            'tienNghi' => $row['tienNghi'],
            'dienTich' => $row['dienTich'],
            'sucChua' => $row['sucChua']
        ];
    }
}
echo json_encode($phong, JSON_UNESCAPED_UNICODE);
$conn->close();
