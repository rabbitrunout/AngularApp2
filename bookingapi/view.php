<?php
require 'connect.php';  // Подключение к базе

// Получаем ID бронирования из GET-параметра (название параметра изменил на ID)
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing ID']);
    exit;
}

// SQL-запрос к таблице бронирований (предположим, что таблица называется 'reservations')
$sql = "SELECT * FROM reservations WHERE ID = {$id} LIMIT 1";

if ($result = mysqli_query($con, $sql)) {
    if (mysqli_num_rows($result) == 1) {
        $reservation = mysqli_fetch_assoc($result);
        header('Content-Type: application/json');
        echo json_encode($reservation);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Reservation not found']);
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed']);
}
?>
