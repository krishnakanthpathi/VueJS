
<?php include 'db_connection.php'; ?>
<!-- Array
(
    [0] => Train No
    [1] => Train Name
    [2] => SEQ
    [3] => Station Code
    [4] => Station Name
    [5] => Arrival time
    [6] => Departure Time
    [7] => Distance
    [8] => Source Station
    [9] => Source Station Name
    [10] => Destination Station
    [11] => Destination Station Name
) -->
<?php 
    // inserting trains into database
    foreach ($rows as $row) {
        // splitting each row into columns
        $columns = str_getcsv($row);
        $station_code = $columns[3];
        $station_name = $columns[4];


        $query = "INSERT IGNORE INTO stations (station_code, station_name) 
                  VALUES ('$station_code', '$station_name')";

        if ($conn->query($query) === TRUE) {
            echo "Inserted station: $station_code - $station_name\n";
        } else {
            error_log("Error inserting station $station_code: " . $conn->error);
            echo "Error: " . $conn->error . "\n";
            echo "Error inserting station: $station_code\n";
        }
        
    }

?>