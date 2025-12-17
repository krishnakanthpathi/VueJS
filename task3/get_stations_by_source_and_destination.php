

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



<?php 
    // get trains between two stations
    function get_trains_between_stations($conn, $source_code, $destination_code , $page_no = 1) {
        $page_size = 10;
        $offset = ($page_no - 1) * $page_size;
        $query = "SELECT t.train_number, t.train_name
                  FROM trains t
                  JOIN schedules s1 ON t.train_number = s1.train_number
                  JOIN schedules s2 ON t.train_number = s2.train_number
                  WHERE s1.station_code = '$source_code'
                  AND s2.station_code = '$destination_code'
                  AND s1.seq < s2.seq    
                  LIMIT $page_size 
                  OFFSET $offset        
        ";
        $len_query = "SELECT COUNT(*) as total
                  FROM trains t
                  JOIN schedules s1 ON t.train_number = s1.train_number
                  JOIN schedules s2 ON t.train_number = s2.train_number
                  WHERE s1.station_code = '$source_code'
                  AND s2.station_code = '$destination_code'
                  AND s1.seq < s2.seq";

        $result = mysqli_query($conn, $query);
        $len_result = mysqli_query($conn, $len_query);
        
        // alternate way to run the query
        
        $response = [];

        if ($len_result) {
            $len_row = mysqli_fetch_assoc($len_result);
            $response['total_trains'] = $len_row['total'];
        } else {
            $response['total_trains'] = 0;
        }

        
        if (!$result) {
            // detailed error for debugging
            die("Query Failed: " . $conn->error); 
        }

        try {
            $trains = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $trains[] = [
                        'train_no' => $row['train_number'],
                        'train_name' => $row['train_name']
                ];
            } 
            $response['trains'] = $trains;
        } catch (Exception $e) {
            echo("Error fetching trains: " . e.getMessage()); 
            return [];
        }

        return $response;
    }


    // get request for in between stations for source and destination
    if (isset($_GET['source']) && isset($_GET['destination'])) {
        $source = $_GET['source'];
        $destination = $_GET['destination'];
        $page_no = isset($_GET['page']) ? intval($_GET['page']) : 1;

        $trains = get_trains_between_stations($conn, $source, $destination , $page_no);
        echo json_encode($trains);
    } else {
        echo json_encode(['error' => 'Source and Destination parameters are required.']);
    }


    


?>