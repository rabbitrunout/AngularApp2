<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

require_once 'connect.php';

if (!$con) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit;
}

$response = ['success' => false, 'message' => 'Ошибка регистрации'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $inputJSON = file_get_contents('php://input');
  $input = json_decode($inputJSON, true);

  $userName = trim($input['userName'] ?? '');
  $email = trim($input['email'] ?? '');
  $password = trim($input['password'] ?? '');

  if (empty($userName) || empty($email) || empty($password)) {
    $response['message'] = 'Все поля обязательны для заполнения';
    echo json_encode($response);
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Неверный формат email';
    echo json_encode($response);
    exit;
  }

  $stmt = $con->prepare("SELECT ID FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $response['message'] = 'Пользователь с таким email уже зарегистрирован';
    echo json_encode($response);
    exit;
  }

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $con->prepare("INSERT INTO users (userName, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $userName, $email, $hashedPassword);

  if (!$stmt->execute()) {
    $response['message'] = 'Ошибка при регистрации: ' . $stmt->error;
    echo json_encode($response);
    exit;
  }

  $response['success'] = true;
  $response['message'] = 'Регистрация прошла успешно';
} else {
  $response['message'] = 'Неверный метод запроса';
}

echo json_encode($response);
?>
