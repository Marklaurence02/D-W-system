<?php
include_once "../assets/config.php";

if (isset($_GET['table_id'], $_GET['date'])) {
    $tableId = intval($_GET['table_id']);
    $date = $_GET['date'];

    $query = "SELECT reservation_time FROM reservations WHERE table_id = ? AND reservation_date = ? AND status = 'Paid'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $tableId, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $unavailableTimes = [];
    while ($row = $result->fetch_assoc()) {
        $unavailableTimes[] = substr($row['reservation_time'], 0, 5); // Get time in HH:MM format
    }

    echo json_encode(['unavailable' => $unavailableTimes]);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid parameters']);
}
?>
