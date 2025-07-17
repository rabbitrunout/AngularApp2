<?php
require 'connect.php';
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Получение и очистка данных
$ID         = isset($_POST['ID']) ? (int) $_POST['ID'] : 0;
$location   = mysqli_real_escape_string($con, $_POST['location'] ?? '');
$start_time = mysqli_real_escape_string($con, $_POST['start_time'] ?? '');
$end_time   = mysqli_real_escape_string($con, $_POST['end_time'] ?? '');
$complete   = isset($_POST['complete']) ? (int) $_POST['complete'] : 0;
$originalImageName = mysqli_real_escape_string($con, $_POST['originalImageName'] ?? '');
$imageName = $originalImageName;

// Валидация обязательных полей
if ($ID < 1 || $location === '' || $start_time === '' || $end_time === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields.']);
    exit;
}

// Проверка и загрузка нового изображения
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $newImageName = basename($_FILES['image']['name']);

    // Проверка дубликата имени изображения, исключая текущую запись
    if ($newImageName !== 'placeholder.jpg') {
        $imageCheckQuery = "SELECT ID FROM reservations WHERE image_name = '$newImageName' AND ID != $ID LIMIT 1";
        $imageCheckResult = mysqli_query($con, $imageCheckQuery);
        if ($imageCheckResult && mysqli_num_rows($imageCheckResult) > 0) {
            http_response_code(409);
            echo json_encode(['error' => 'Image name already exists.']);
            exit;
        }
    }

    // Сохранение нового изображения
    $targetFilePath = $uploadDir . $newImageName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
        // Удаление старого изображения (если не плейсхолдер)
        if (!empty($originalImageName) && $originalImageName !== 'placeholder.jpg' && file_exists($uploadDir . $originalImageName)) {
            unlink($uploadDir . $originalImageName);
        }
        $imageName = $newImageName;
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload image.']);
        exit;
    }
}

// Обновление записи
$sql = "UPDATE reservations SET 
            location = '$location',
            start_time = '$start_time',
            end_time = '$end_time',
            complete = $complete,
            image_name = '$imageName'
        WHERE ID = $ID
        LIMIT 1";

if (mysqli_query($con, $sql)) {
    http_response_code(200);
    echo json_encode(['message' => 'Reservation updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database update failed']);
}
?>
