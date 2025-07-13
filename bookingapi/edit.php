<?php
  require 'connect.php';

  // Get the posted data
  $postdata = file_get_contents("php://input");

  if (isset($postdata) && !empty($postdata)) {
    // Extract the data
    $request = json_decode($postdata);

    // Validate
    if ((int)$request->data->ID < 1 ||
        trim($request->data->location) === '' ||
        trim($request->data->start_time) === '' ||
        trim($request->data->end_time) === '') {
      return http_response_code(400);
    }

    // Sanitize
    $ID = mysqli_real_escape_string($con, (int)$request->data->ID);
    $location = mysqli_real_escape_string($con, trim($request->data->location));
    $start_time = mysqli_real_escape_string($con, trim($request->data->start_time));
    $end_time = mysqli_real_escape_string($con, trim($request->data->end_time));
    $complete = isset($request->data->complete) ? (int)$request->data->complete : 0;
    $imageName = isset($request->data->imageName) ? mysqli_real_escape_string($con, trim($request->data->imageName)) : 'placeholder.jpg';

    // Update query
    $sql = "UPDATE `reservations` 
            SET `location` = '$location', 
                `start_time` = '$start_time', 
                `end_time` = '$end_time', 
                `reserved` = '$complete', 
                `imageName` = '$imageName' 
            WHERE `ID` = '$ID' 
            LIMIT 1";

    if (mysqli_query($con, $sql)) {
      http_response_code(204);
    } else {
      http_response_code(422);
    }
  }
?>