    <?php

require 'connect.php';

$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {

    // Декодируем JSON
    $request = json_decode($postdata);

    // Валидация данных
    if (trim($request->location) === '' || trim($request->start_time) === '' || trim($request->end_time) === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // Экранируем входные данные
    $location = mysqli_real_escape_string($con, $request->location);
    $start_time = mysqli_real_escape_string($con, $request->start_time);
    $end_time = mysqli_real_escape_string($con, $request->end_time);
    $complete = isset($request->complete) ? (int)$request->complete : 0;
    $imageName = isset($request->imageName) ? basename(str_replace('\\', '/', $request->imageName)) : '';

    // Если имя изображения не указано — использовать заглушку
    if (empty($imageName)) {
        $imageName = 'placeholder.jpg';
    }

    // SQL-запрос
    $sql = "INSERT INTO reservations (location, start_time, end_time, complete, image_name)
            VALUES ('$location', '$start_time', '$end_time', $complete, '$imageName')";

    // Выполнение и возврат ответа
    if (mysqli_query($con, $sql)) {
        $id = mysqli_insert_id($con);

        $reservation = [
            'ID' => $id,
            'location' => $location,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'complete' => $complete,
            'imageName' => $imageName
        ];

        http_response_code(201);
        echo json_encode($reservation);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database insert failed']);
    }
}
?>
