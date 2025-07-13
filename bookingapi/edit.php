<?php
    require 'connect.php';

    // Get the posted data
    $postdata = file_get_contents("php://input");

    if (isset($postdata) && !empty($postdata))
    {
        // Extract the data
    $request = json_decode($postdata);

       if (
            !isset($request->ID) || 
            trim($request->location) === '' || 
            trim($request->start_time) === '' ||
            trim($request->end_time) === ''
            ) {
                return http_response_code(400);
            }

        $ID = (int)$request->ID; // <-- это обязательно
$location = mysqli_real_escape_string($con, $request->location);
$start_time = mysqli_real_escape_string($con, $request->start_time);
$end_time = mysqli_real_escape_string($con, $request->end_time);
$complete = isset($request->complete) ? (int)$request->complete : 0;

$sql = "UPDATE `reservations`
        SET `location`='$location',
            `start_time`='$start_time',
            `end_time`='$end_time',
            `complete`='$complete'
        WHERE `ID` = $ID
        LIMIT 1";

        if(mysqli_query($con, $sql))
        {
            http_response_code(204);
        }
        else
        {
            http_response_code(422);
        }
    }
?>