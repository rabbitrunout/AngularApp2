<?php
require 'connect.php';

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Только POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Получение данных
  $location = isset($_POST['location']) ? mysqli_real_escape_string($con, trim($_POST['location'])) : '';
  $start_time = isset($_POST['start_time']) ? mysqli_real_escape_string($con, trim($_POST['start_time'])) : '';
  $end_time = isset($_POST['end_time']) ? mysqli_real_escape_string($con, trim($_POST['end_time'])) : '';
  $complete = isset($_POST['complete']) ? (int)$_POST['complete'] : 0;

  // Валидация
  if ($location === '' || $start_time === '' || $end_time === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields.']);
    exit;
  }

  // Проверка на дублирование записи
  $checkSql = "SELECT 1 FROM reservations 
               WHERE location = '{$location}' 
               AND start_time = '{$start_time}' 
               AND end_time = '{$end_time}' 
               LIMIT 1";

  $checkResult = mysqli_query($con, $checkSql);
  if (mysqli_num_rows($checkResult) > 0) {
    http_response_code(409);
    echo json_encode(['error' => 'A reservation already exists for this location and time.']);
    exit;
  }

  // Загрузка изображения
  $imageName = 'placeholder.jpg';
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $target_dir = 'uploads/';
    $originalName = $_FILES['image']['name'];
    $tmpName = $_FILES['image']['tmp_name'];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    $finalName = $baseName;
    $i = 1;

    // Предотвращение перезаписи файла
    while (file_exists($target_dir . $finalName . '.' . $ext)) {
      $finalName = $baseName . '_' . $i++;
    }

    $finalFilePath = $target_dir . $finalName . '.' . $ext;

    if (move_uploaded_file($tmpName, $finalFilePath)) {
      $imageName = $finalName . '.' . $ext;
    } else {
      http_response_code(500);
      echo json_encode(['error' => 'Image upload failed.']);
      exit;
    }
  }

  // Вставка данных
  $sql = "INSERT INTO `reservations` (`location`, `start_time`, `end_time`, `complete`, `image_name`) 
          VALUES ('{$location}', '{$start_time}', '{$end_time}', {$complete}, '{$imageName}')";

  if (mysqli_query($con, $sql)) {
    http_response_code(201);
    echo json_encode([
      'data' => [
        'id' => mysqli_insert_id($con),
        'location' => $location,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'complete' => $complete,
        'image_name' => $imageName
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode(['error' => 'Database insert failed.']);
  }

} else {
  http_response_code(405);
  echo json_encode(['error' => 'Method Not Allowed']);
}
?>
