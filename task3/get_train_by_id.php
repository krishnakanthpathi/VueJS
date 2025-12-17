

<?php include 'db_connection.php'; ?>


<?php
    // inserting stations into database
    // 	station_code	station_name	
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
    //   schedules table
    //   `train_number` varchar(10) NOT NULL,
    //   `seq` int(11) NOT NULL,
    //   `station_code` varchar(10) DEFAULT NULL,
    //   `arrival_time` time DEFAULT NULL,
    //   `departure_time` time DEFAULT NULL,
    //   `distance` int(11) DEFAULT NULL

    // stations table
    // 	station_code Primary	
	//  station_name
    
    // trains table
    // 1	train_number Primary	
	// 2	train_name	varchar(100)	
	// 3	source_station_code Index	
	// 4	destination_station_code Index	


?>

<?php include 'get_full_train_schedule.php'; ?>


<?php 
    // get train by id
    function get_train_by_id($conn, $train_id) {
        // getting all the information about the train 
        $query = "SELECT train_number, train_name, source_station_code, destination_station_code 
                  FROM trains 
                  WHERE train_number = '$train_id'";
        
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        } else {
            return null;
        }
    }


    // get request for train by id
    if (isset($_GET['train_id'])) {
        $train_id = $_GET['train_id'];
        $train = get_train_by_id($conn, $train_id);

        if ($train) {
            $schedule = get_full_train_schedule($conn, $train_id);
            $train['schedule'] = $schedule;
            
            echo json_encode($train);
        } else {
            echo json_encode(null);
            error_log("Train not found for ID: $train_id");
        }
    }
    


    


?>