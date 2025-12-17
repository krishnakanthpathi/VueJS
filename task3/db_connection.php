
<?php
    $conn = new mysqli("localhost", "root", "", "trains_db");
    if ($conn->connect_error) {
        // Connection failed
        error_log("Connection failed: " . $conn->connect_error);
        echo "Database connection error. Please try again later.";
        exit();
    }

    // operations.php

    // setting content type to plain and text
    header('Content-Type: application/json');  


    // importing file contents
    // $trains_data = file_get_contents('trains_dataset.csv');

    // // splitting data into rows
    // $rows = explode("\n", trim($trains_data));

    // Array
    // (
    //     [0] => Train No
    //     [1] => Train Name
    //     [2] => SEQ
    //     [3] => Station Code
    //     [4] => Station Name
    //     [5] => Arrival time
    //     [6] => Departure Time
    //     [7] => Distance
    //     [8] => Source Station
    //     [9] => Source Station Name
    //     [10] => Destination Station
    //     [11] => Destination Station Name
    // )

?>