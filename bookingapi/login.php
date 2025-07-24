<?php
session_start();

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require 'connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->userName, $data->password)) {
    echo json_encode(['success' => false, 'message' => 'Missing credentials']);
    exit;
}

$username = trim($data->userName);
$password = trim($data->password);

// Используем таблицу users (как в register.php)
$query = $con->prepare("SELECT * FROM users WHERE userName = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['loggedIn'] = true;
        $_SESSION['username'] = $username;

        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>
