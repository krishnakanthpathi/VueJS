
<?php include 'db_connection.php'; ?>


<?php
    // inserting trains into database
    // 	train_number	train_name	source_station_code	destination_station_code	
	
    //     Array
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
    foreach ($rows as $row) {
        // splitting each row into columns
        $columns = str_getcsv($row);

        $train_number = $columns[0];
        $train_name = $columns[1];
        $source_station_code = $columns[8];
        $destination_station_code = $columns[10];

        $query = "INSERT INTO trains (train_number, train_name, source_station_code, destination_station_code) 
                  VALUES ('$train_number', '$train_name', '$source_station_code', '$destination_station_code')";

        if ($conn->query($query) === TRUE) {
            echo "Inserted train: $train_number - $train_name\n";
        } else {
            error_log("Error inserting train $train_number: " . $conn->error);
            echo "Error: " . $conn->error . "\n";
            echo "Error inserting train: $train_number - $train_name\n";
        }


    }
?>

