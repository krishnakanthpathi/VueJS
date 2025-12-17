

<?php include 'db_connection.php'; ?>


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

    function get_trains_by_station($conn , $station_code) {
        $trains = [];

        $query = "SELECT DISTINCT t.train_number, t.train_name 
                  FROM trains t
                  JOIN schedules s ON t.train_number = s.train_number
                  WHERE s.station_code = '$station_code'";

        $result = mysqli_query($conn, $query);

        try {
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $trains[] = $row;
                }
            }
        } catch (Exception $e) {
            error_log("Error fetching trains for station $station_code: " . $e->getMessage());
            echo "Error fetching trains. Please try again later.";
        }

        return $trains;
    }

    // get request for trains by station
    if (isset($_GET['station_code'])) {
        $station_code = $_GET['station_code'];
        $trains = get_trains_by_station($conn, $station_code);
        echo json_encode($trains);
    }

?>

