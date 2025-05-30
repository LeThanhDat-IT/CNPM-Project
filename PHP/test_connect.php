<?php
require_once 'connect.php';

// Thay 'bang_khachhang' bằng tên bảng thực tế của bạn
$sql = "SELECT * FROM bookings";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '<pre>';
        print_r($row);
        echo '</pre>';
    }
} else {
    echo "Không có dữ liệu hoặc lỗi truy vấn!";
}
$conn->close();
?>
