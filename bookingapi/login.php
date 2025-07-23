<?php
session_start();
require_once 'connect.php';

header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'message' => 'Ошибка авторизации'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if (empty($email) || empty($password)) {
    $response['message'] = 'Email и пароль обязательны';
    echo json_encode($response);
    exit;
  }

  $stmt = $con->prepare("SELECT ID, password, userName FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 0) {
    $response['message'] = 'Пользователь не найден';
    echo json_encode($response);
    exit;
  }

  $stmt->bind_result($userID, $hashedPassword, $userName);
  $stmt->fetch();

  if (password_verify($password, $hashedPassword)) {
    $_SESSION['userID'] = $userID;
    $_SESSION['userName'] = $userName;

    $response['success'] = true;
    $response['message'] = 'Авторизация успешна';
  } else {
    $response['message'] = 'Неверный пароль';
  }
} else {
  $response['message'] = 'Неверный метод запроса';
}

echo json_encode($response);
?>