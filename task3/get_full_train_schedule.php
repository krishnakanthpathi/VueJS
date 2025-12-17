
<?php include 'db_connection.php'; ?>

<?php 
        function get_full_train_schedule($conn, $train_id) {
        $query = "SELECT s.seq, s.station_code, st.station_name, s.arrival_time, s.departure_time, s.distance
                  FROM schedules s
                  JOIN stations st 
                  ON s.station_code = st.station_code
                  WHERE s.train_number = '$train_id'
                  ORDER BY s.seq ASC";
        
        $result = mysqli_query($conn, $query);
        $schedule = [];

        try {
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $schedule[] = $row;
                }
            }
        } catch (Exception $e) {
            error_log("Error fetching schedule for train $train_id: " . $e->getMessage());
            echo "Error fetching schedule. Please try again later.";
        }
        
        return $schedule;
    }
?>