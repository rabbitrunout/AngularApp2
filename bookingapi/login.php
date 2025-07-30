<?php
date_default_timezone_set('America/Toronto');
session_start();

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Credentials: true');
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

$query = $con->prepare("SELECT * FROM users WHERE userName = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();

if ($result->num_rows !== 1) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$user = $result->fetch_assoc();

$failedAttempts = (int)$user['failed_attempts'];
$lastFailed = $user['last_failed_login'] ? strtotime($user['last_failed_login']) : null;
$currentTime = time();

// Проверка блокировки
if ($failedAttempts >= 3 && $lastFailed !== null) {
    $diff = $currentTime - $lastFailed;
    if ($diff < 300) {
        $remaining = 300 - $diff;
        echo json_encode([
            'success' => false,
            'message' => "Слишком много неудачных попыток. Подождите {$remaining} секунд.",
            'waitTime' => $remaining
        ]);
        exit;
    } else {
        // Сброс блокировки
        $reset = $con->prepare("UPDATE users SET failed_attempts = 0, last_failed_login = NULL WHERE ID = ?");
        $reset->bind_param("i", $user['ID']);
        $reset->execute();
        $failedAttempts = 0;
    }
}

// Проверка пароля
if (password_verify($password, $user['password'])) {
    $_SESSION['loggedIn'] = true;
    $_SESSION['userID'] = $user['ID'];
    $_SESSION['userName'] = $user['userName'];

    $reset = $con->prepare("UPDATE users SET failed_attempts = 0, last_failed_login = NULL WHERE ID = ?");
    $reset->bind_param("i", $user['ID']);
    $reset->execute();

    echo json_encode(['success' => true, 'message' => 'Login successful']);
} else {
    $update = $con->prepare("UPDATE users SET failed_attempts = failed_attempts + 1, last_failed_login = NOW() WHERE ID = ?");
    $update->bind_param("i", $user['ID']);
    $update->execute();

    $failedAttempts++;
    $remainingAttempts = max(0, 3 - $failedAttempts);

    if ($remainingAttempts === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Слишком много неудачных попыток. Подождите 300 секунд.',
            'waitTime' => 300
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Неверный пароль.',
            'remainingAttempts' => $remainingAttempts
        ]);
    }
}
?>
