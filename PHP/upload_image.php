<?php
$targetDir = "../images/";
if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

if (isset($_FILES["file"])) {
    $fileName = basename($_FILES["file"]["name"]);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($imageFileType, $allowTypes)) {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            echo json_encode([
                "success" => true,
                "path" => "images/" . $fileName
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Không thể upload ảnh."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Chỉ cho phép file ảnh."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Không có file."]);
}
?>