<?php
require 'connect.php';

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $location = isset($_POST['location']) ? mysqli_real_escape_string($con, trim($_POST['location'])) : '';
    $start_time = isset($_POST['start_time']) ? mysqli_real_escape_string($con, trim($_POST['start_time'])) : '';
    $end_time = isset($_POST['end_time']) ? mysqli_real_escape_string($con, trim($_POST['end_time'])) : '';
    $complete = isset($_POST['complete']) ? (int) $_POST['complete'] : 0;

    if ($location === '' || $start_time === '' || $end_time === '') {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required fields']);
        exit();
    }

    $imageName = 'placeholder.jpg';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = 'uploads/';
        $originalName = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $finalName = $baseName;
        $i = 1;

        while (file_exists($target_dir . $finalName . '.' . $ext)) {
            $finalName = $baseName . '_' . $i++;
        }

        $finalFilePath = $target_dir . $finalName . '.' . $ext;

        if (move_uploaded_file($tmpName, $finalFilePath)) {
            $imageName = $finalName . '.' . $ext;
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Image upload failed']);
            exit();
        }
    }

    $sql = "INSERT INTO reservations (location, start_time, end_time, complete, image_name)
            VALUES ('$location', '$start_time', '$end_time', $complete, '$imageName')";

    if (mysqli_query($con, $sql)) {
        echo json_encode(['ID' => mysqli_insert_id($con)]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Insert failed']);
    }

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}
?>
