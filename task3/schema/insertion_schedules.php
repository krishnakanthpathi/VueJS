
<?php include 'db_connection.php'; ?>

<?php   
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


<?php 
    // inserting trains into database
    // inserting schedules into database
    // train_number	seq	station_code	arrival_time	departure_time	distance	
    
    foreach ($rows as $row) {
        // splitting each row into columns
        $columns = str_getcsv($row);
        $train_number = $columns[0];
        $seq = $columns[2];
        $station_code = $columns[3];
        $arrival_time = $columns[5];
        $departure_time = $columns[6];
        $distance = $columns[7];
        
        $query = "INSERT INTO schedules (train_number, seq, station_code, arrival_time, departure_time, distance) 
                  VALUES ('$train_number', '$seq', '$station_code', '$arrival_time', '$departure_time', '$distance')";

        if ($conn->query($query) === TRUE) {
            echo "Inserted schedule: $train_number - $station_code\n";
        } else {
            error_log("Error inserting schedule for $train_number at $station_code: " . $conn->error);
            echo "Error: " . $conn->error . "\n";
            echo "Error inserting schedule: $train_number - $station_code\n";
        }
        
    }
?>
