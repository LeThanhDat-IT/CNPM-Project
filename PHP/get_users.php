<?php
require_once 'connect.php';
header('Content-Type: application/json; charset=utf-8');

$result = $conn->query("SELECT * FROM users");
$list = [];
while ($row = $result->fetch_assoc()) {
    $list[] = $row;
}
echo json_encode($list);
$conn->close();
?>