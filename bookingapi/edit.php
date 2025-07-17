<?php
require 'connect.php';

header('Content-Type: application/json');
mysqli_set_charset($con, 'utf8mb4');

$id           = $_POST['ID'] ?? 0;
$location     = $_POST['location'] ?? '';
$start_time   = $_POST['start_time'] ?? '';
$end_time     = $_POST['end_time'] ?? '';
$complete     = $_POST['complete'] ?? 0;
$existingImage = $_POST['existingImage'] ?? '';
$imageName    = $existingImage; // default = existing

// Если загружен новый файл — сохраняем его и переопределяем imageName
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['image']['name']);
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    $baseName = pathinfo($fileName, PATHINFO_FILENAME);
    $i = 1;
    $targetPath = $uploadDir . $fileName;

    while (file_exists($targetPath)) {
        $fileName = $baseName . '_' . $i++ . '.' . $ext;
        $targetPath = $uploadDir . $fileName;
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $imageName = $fileName; // override
    }
}

// Обновление в базе
$sql = "UPDATE reservations SET 
            location = ?, 
            start_time = ?, 
            end_time = ?, 
            complete = ?, 
            image_name = ?
        WHERE ID = ?";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "sssisi", $location, $start_time, $end_time, $complete, $imageName, $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['message' => 'Reservation updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update']);
}
?>
