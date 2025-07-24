<?php

// CORS заголовки
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Content-Type: application/json; charset=utf-8');

// Обработка preflight запроса
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'connect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Логируем входящие данные для отладки
// file_put_contents('debug_edit.txt', "=== New Request ===\n" . print_r($_POST, true) . "\nFILES:\n" . print_r($_FILES, true) . "\n", FILE_APPEND);

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Получение и проверка ID
$id = isset($_POST['ID']) ? (int)$_POST['ID'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid reservation ID.']);
    exit;
}

// Получение и очистка данных
$location = trim($_POST['location'] ?? '');
$start_time = trim($_POST['start_time'] ?? '');
$end_time = trim($_POST['end_time'] ?? '');
$complete = (isset($_POST['complete']) && ($_POST['complete'] == '1' || $_POST['complete'] === 1)) ? 1 : 0;
$existingImage = trim($_POST['existingImage'] ?? 'placeholder.jpg');

// Проверка обязательных полей
if ($location === '' || $start_time === '' || $end_time === '') {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required.']);
    exit;
}

// Проверка на дубликат (исключая текущую запись)
$checkSql = "SELECT ID FROM reservations 
             WHERE location = ? AND start_time = ? AND end_time = ? AND ID != ? 
             LIMIT 1";
$checkStmt = $con->prepare($checkSql);
if (!$checkStmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare duplicate check statement.', 'mysql_error' => $con->error]);
    exit;
}
$checkStmt->bind_param("sssi", $location, $start_time, $end_time, $id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult && $checkResult->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['error' => 'A reservation already exists for this location and time.']);
    $checkStmt->close();
    $con->close();
    exit;
}
$checkStmt->close();

// Обработка загрузки изображения
$uploadDir = 'uploads/';
$imageName = $existingImage;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $originalName = $_FILES['image']['name'];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    $finalName = $baseName;
    $counter = 1;

    // Создаём уникальное имя файла, чтобы не перезаписать существующий
    $destPath = $uploadDir . $finalName . '.' . $ext;
    while (file_exists($destPath)) {
        $finalName = $baseName . '_' . $counter++;
        $destPath = $uploadDir . $finalName . '.' . $ext;
    }

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        // Удаляем старое изображение, если оно не placeholder и отличается от нового
        if ($existingImage && $existingImage !== 'placeholder.jpg' && $existingImage !== $finalName . '.' . $ext) {
            $oldPath = $uploadDir . $existingImage;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
        $imageName = $finalName . '.' . $ext;
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to move uploaded file.']);
        exit;
    }
}

// Подготовка запроса обновления
$updateSql = "UPDATE reservations 
              SET location = ?, start_time = ?, end_time = ?, complete = ?, image_name = ? 
              WHERE ID = ?";

$stmt = $con->prepare($updateSql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare update statement.', 'mysql_error' => $con->error]);
    exit;
}

// Привязываем параметры
$stmt->bind_param("sssisi", $location, $start_time, $end_time, $complete, $imageName, $id);

// Выполняем запрос
if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Reservation updated successfully.']);
} else {
    $errorMsg = $stmt->error;
    file_put_contents('debug_edit.txt', "Update error: " . $errorMsg . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update reservation.', 'mysql_error' => $errorMsg]);
}

$stmt->close();
$con->close();

?>
