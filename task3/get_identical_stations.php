

<?php include 'db_connection.php'; ?>


<?php

    function get_identical_stations($conn , $keyword) {
        $stations = [];

        $query = "SELECT station_code, station_name 
                  FROM stations
                  WHERE station_name LIKE '%$keyword%'
                  OR station_code LIKE '%$keyword%' 
                  ORDER BY station_name ASC
                  LIMIT 50
                ";

        $result = mysqli_query($conn, $query);

        try {
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $stations[] = $row;
                }
            }
        } catch (Exception $e) {
            error_log("Error fetching identical stations for $keyword: " . $e->getMessage());
            echo "Error fetching stations. Please try again later.";
        }

        return $stations;
    }

    // get request for identical stations
    if (isset($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
        $stations = get_identical_stations($conn, $keyword);
        echo json_encode($stations);
    }
    
    

?>

