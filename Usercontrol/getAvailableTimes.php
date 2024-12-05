<?php
session_name("user_session");
session_start();
include_once "../assets/config.php";

if (isset($_GET['table_id'], $_GET['date'])) {
    $tableId = intval($_GET['table_id']);
    $date = $_GET['date'];

    // Query to get reserved times for the given table and date
    $query = "SELECT reservation_time FROM reservations WHERE table_id = ? AND reservation_date = ? AND status = 'Reserved'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $tableId, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    // Array to store unavailable times in HH:MM format
    $unavailableTimes = [];
    while ($row = $result->fetch_assoc()) {
        $unavailableTimes[] = substr($row['reservation_time'], 0, 5); // Get the time part (HH:MM)
    }

    // Output unavailable times in JSON format
    echo json_encode(['unavailable' => $unavailableTimes]);

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid parameters']);
}
?>
