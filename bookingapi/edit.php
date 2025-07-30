<?php
// CORS и заголовки
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Content-Type: application/json; charset=utf-8');

// Preflight-запрос (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'connect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Только POST-запрос
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Получение и валидация ID
$id = isset($_POST['ID']) ? (int)$_POST['ID'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid reservation ID.']);
    exit;
}

// Получение данных
$location = trim($_POST['location'] ?? '');
$start_time = trim($_POST['start_time'] ?? '');
$end_time = trim($_POST['end_time'] ?? '');
$complete = (isset($_POST['complete']) && ($_POST['complete'] == '1' || $_POST['complete'] === 1)) ? 1 : 0;
$existingImage = mysqli_real_escape_string($con, trim($_POST['existingImage'] ?? 'placeholder.jpg'));
$imageName = $existingImage;
$originalImageName = mysqli_real_escape_string($con, $_POST['oldImageName'] ?? '');
$imageName = $originalImageName;


// Проверка обязательных полей
if ($location === '' || $start_time === '' || $end_time === '') {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required.']);
    exit;
}

// Проверка на пересечение с другими бронированиями
$checkSql = "SELECT ID FROM reservations 
             WHERE location = ? 
               AND ID != ? 
               AND (start_time < ? AND end_time > ?) 
             LIMIT 1";

$stmt = $con->prepare($checkSql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare duplicate check.', 'mysql_error' => $con->error]);
    exit;
}

$stmt->bind_param("siss", $location, $id, $end_time, $start_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['error' => 'Another reservation already exists at this time and location.']);
    $stmt->close();
    $con->close();
    exit;
}
$stmt->close();

// Обработка изображения
$uploadDir = 'uploads/';
$imageName = $existingImage;  // изначально - имя старого файла

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmpPath = $_FILES['image']['tmp_name'];
    $originalName = $_FILES['image']['name'];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);

    // Создаем уникальное имя файла
    $finalName = $baseName;
    $counter = 1;
    $destPath = $uploadDir . $finalName . '.' . $ext;
    while (file_exists($destPath)) {
        $finalName = $baseName . '_' . $counter++;
        $destPath = $uploadDir . $finalName . '.' . $ext;
    }

    if (move_uploaded_file($tmpPath, $destPath)) {
        // Удаляем старый файл, если он существует, не является плейсхолдером и отличается от нового файла
        if (
            $existingImage &&
            $existingImage !== 'placeholder.jpg' &&
            $existingImage !== basename($destPath)
        ) {
            $oldPath = $uploadDir . $existingImage;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
        // Обновляем имя картинки для записи в базу
        $imageName = basename($destPath);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to move uploaded file.']);
        exit;
    }
}


// Обновление записи
$updateSql = "UPDATE reservations 
              SET location = ?, start_time = ?, end_time = ?, complete = ?, image_name = ?
              WHERE ID = ?";

$stmt = $con->prepare($updateSql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare update statement.', 'mysql_error' => $con->error]);
    exit;
}

$stmt->bind_param("sssisi", $location, $start_time, $end_time, $complete, $imageName, $id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Reservation updated successfully.',
        'reservation' => [
            'ID' => $id,
            'location' => $location,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'complete' => $complete,
            'imageName' => $imageName
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update reservation.', 'mysql_error' => $stmt->error]);
}

$stmt->close();
$con->close();
?>