<?php
date_default_timezone_set('America/Toronto');
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

$query = $con->prepare("SELECT * FROM users WHERE userName = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    $failedAttempts = (int)$user['failed_attempts'];
    $lastFailed = $user['last_failed_login'];

   if ($failedAttempts >= 3 && $lastFailed) {
    $lastFailedTime = strtotime($lastFailed);
    $currentTime = time();
    $timeDiff = $currentTime - $lastFailedTime;
    
    // DEBUG:
    error_log("lastFailedTime: $lastFailedTime; currentTime: $currentTime; timeDiff: $timeDiff");
    
    if ($timeDiff < 300) {
        $remaining = 300 - $timeDiff;
        echo json_encode([
            'success' => false,
            'message' => "Слишком много неудачных попыток. Подождите {$remaining} сек.",
            'waitTime' => $remaining
        ]);
        exit;
    }
}


    if (password_verify($password, $user['password'])) {
        // успешный вход — сбрасываем счётчик
        $_SESSION['loggedIn'] = true;
        $_SESSION['username'] = $username;

        $reset = $con->prepare("UPDATE users SET failed_attempts = 0, last_failed_login = NULL WHERE ID = ?");
        $reset->bind_param("i", $user['ID']);
        $reset->execute();

        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        // неудачный вход — увеличиваем счётчик
        error_log("Failed attempts: $failedAttempts; last failed: $lastFailed");

        $update = $con->prepare("UPDATE users SET failed_attempts = failed_attempts + 1, last_failed_login = NOW() WHERE ID = ?");
        $update->bind_param("i", $user['ID']);
        $update->execute();

        // Получаем обновлённые данные, чтобы проверить количество попыток
        error_log("Time difference: $timeDiff seconds");

        $check = $con->prepare("SELECT failed_attempts, last_failed_login FROM users WHERE ID = ?");
        $check->bind_param("i", $user['ID']);
        $check->execute();
        $resCheck = $check->get_result();
        $rowCheck = $resCheck->fetch_assoc();

        $failedAttempts = (int)$rowCheck['failed_attempts'];
        $lastFailed = $rowCheck['last_failed_login'];

        if ($failedAttempts >= 3 && $lastFailed) {
            $lastFailedTime = strtotime($lastFailed);
            $currentTime = time();
            $timeDiff = $currentTime - $lastFailedTime;

            if ($timeDiff < 300) {
                $remaining = 300 - $timeDiff;
                echo json_encode([
                    'success' => false,
                    'message' => "Слишком много неудачных попыток. Подождите {$remaining} сек.",
                    'waitTime' => $remaining
                ]);
                exit;
            } else {
                // Таймаут прошёл, сбросить счётчик
                $reset = $con->prepare("UPDATE users SET failed_attempts = 0, last_failed_login = NULL WHERE ID = ?");
                $reset->bind_param("i", $user['ID']);
                $reset->execute();
            }
        }

        // Если блокировки нет, просто вернуть ошибку
        echo json_encode(['success' => false, 'message' => 'Invalid password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>