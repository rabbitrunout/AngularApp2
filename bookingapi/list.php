<?php


header('Access-Control-Allow-Origin: http://localhost:4200');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  
  require 'connect.php';

  $reservations = [];
  $sql = "SELECT id, location, startTime, endTime, complete, imageName FROM reservations";

  if ($result = mysqli_query($con, $sql)) {
    $count = 0;
    while ($row = mysqli_fetch_assoc($result)) {
      $reservations[$count]['id'] = $row['id'];
      $reservations[$count]['location'] = $row['location'];
      $reservations[$count]['startTime'] = $row['startTime'];
      $reservations[$count]['endTime'] = $row['endTime'];
      $reservations[$count]['complete'] = (bool)$row['complete'];
      $reservations[$count]['imageName'] = $row['imageName'];
      $count++;
    }

    echo json_encode(['data' => $reservations]);
  } else {
    http_response_code(404);
    echo json_encode(['error' => 'Failed to retrieve reservations']);
  }
?>