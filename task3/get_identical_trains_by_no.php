
<?php include 'db_connection.php'; ?>


<?php   

    function get_identical_trains_by_no($conn , $keyword) {
        $trains = [];

        $query = "SELECT train_number, train_name 
                  FROM trains
                  WHERE train_number LIKE '%$keyword%' 
                  ORDER BY train_number ASC
                  LIMIT 50
                ";

        $result = mysqli_query($conn, $query);

        try {
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $trains[] = $row;
                }
            }
        } catch (Exception $e) {
            error_log("Error fetching identical trains for $keyword: " . $e->getMessage());
            echo "Error fetching trains. Please try again later.";
        }

        return $trains;
    }
    if(isset($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
        $trains = get_identical_trains_by_no($conn, $keyword);
        echo json_encode($trains);
    }

?>