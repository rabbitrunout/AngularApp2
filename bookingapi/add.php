<?php
require 'connect.php';

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *"); 

// Checking the method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Getting data
$location   = isset($_POST['location']) ? mysqli_real_escape_string($con, trim($_POST['location'])) : '';
$start_time = isset($_POST['start_time']) ? mysqli_real_escape_string($con, trim($_POST['start_time'])) : '';
$end_time   = isset($_POST['end_time']) ? mysqli_real_escape_string($con, trim($_POST['end_time'])) : '';
$complete   = isset($_POST['complete']) ? (int)$_POST['complete'] : 0;

// Validation
if ($location === '' || $start_time === '' || $end_time === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Working with an image
$imageName = 'placeholder.jpg';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $target_dir = 'uploads/';
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = basename($_FILES['image']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $baseName = pathinfo($fileName, PATHINFO_FILENAME);
    $counter = 1;
    $destination = $target_dir . $fileName;

    while (file_exists($destination)) {
        $destination = $target_dir . $baseName . '_' . $counter . '.' . $fileExt;
        $counter++;
    }

    if (move_uploaded_file($fileTmpPath, $destination)) {
        $imageName = basename($destination);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Image upload failed']);
        exit;
    }
}


// Duplicate verification
$checkSql = "SELECT 1 FROM reservations 
             WHERE location = ? AND (start_time < ? AND end_time > ?) LIMIT 1";
$stmt = $con->prepare($checkSql);
$stmt->bind_param("sss", $location, $end_time, $start_time); // pay attention to the order

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['error' => 'A reservation already exists for this location and time.']);
    exit;
}


$stmt->close();

// Inserting a record
$sql = "INSERT INTO reservations (location, start_time, end_time, complete, image_name)
        VALUES ('$location', '$start_time', '$end_time', $complete, '$imageName')";

if (mysqli_query($con, $sql)) {
    $insertedId = mysqli_insert_id($con);

    $response = [
        'ID' => $insertedId,
        'location' => $location,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'complete' => (bool)$complete,
        'imageName' => $imageName
    ];

    http_response_code(201);
    echo json_encode(['data' => $response]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database insert failed']);
}
?>
