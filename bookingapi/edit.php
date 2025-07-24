<?php
require 'connect.php';

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method Not Allowed']);
  exit;
}

// Проверка и получение ID
$id = isset($_POST['ID']) ? (int) $_POST['ID'] : 0;
if ($id <= 0) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid reservation ID.']);
  exit;
}

// Получение и очистка данных
$location = trim($_POST['location'] ?? '');
$start_time = trim($_POST['start_time'] ?? '');
$end_time = trim($_POST['end_time'] ?? '');
$complete = isset($_POST['complete']) && $_POST['complete'] == '1' ? 1 : 0;
$originalImageName = trim($_POST['existingImage'] ?? 'placeholder.jpg');

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
$imageName = $originalImageName;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
  $originalName = $_FILES['image']['name'];
  $tmpName = $_FILES['image']['tmp_name'];
  $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
  $baseName = pathinfo($originalName, PATHINFO_FILENAME);
  $finalName = $baseName;
  $i = 1;

  // Генерация уникального имени
  while (file_exists($uploadDir . $finalName . '.' . $ext)) {
    $finalName = $baseName . '_' . $i++;
  }

  $finalFilePath = $uploadDir . $finalName . '.' . $ext;

  if (move_uploaded_file($tmpName, $finalFilePath)) {
    // Удалить старое изображение (если не placeholder)
    if ($originalImageName !== 'placeholder.jpg') {
      $oldPath = $uploadDir . $originalImageName;
      if (file_exists($oldPath)) {
        unlink($oldPath);
      }
    }
    $imageName = $finalName . '.' . $ext;
  } else {
    http_response_code(500);
    echo json_encode(['error' => 'Image upload failed.']);
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
  echo json_encode(['error' => 'Failed to prepare update statement.']);
  exit;
}

$stmt->bind_param("sssisi", $location, $start_time, $end_time, $complete, $imageName, $id);

if ($stmt->execute()) {
  echo json_encode(['success' => true]);
} else {
  http_response_code(500);
  echo json_encode(['error' => 'Failed to update reservation.']);
}

$stmt->close();
$con->close();
