
<?php
// vieweachReservation.php
    session_start();


include_once "../assets/config.php";

$reservation_id = $_GET['reservation_id'] ?? 0;
$reservationDetails = [];

if ($reservation_id) {
    // Query to fetch reservation details
    $sql = "SELECT r.*, t.table_number, t.seating_capacity, 
                   CONCAT(u.first_name, ' ', u.last_name) AS reserved_by
            FROM reservations r
            LEFT JOIN tables t ON r.table_id = t.table_id
            LEFT JOIN users u ON r.user_id = u.user_id
            WHERE r.reservation_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $reservationDetails = $result->fetch_assoc();
    }

    $stmt->close();
}

if (empty($reservationDetails)) {
    echo "<p>No details available for this reservation.</p>";
} else {
    // Display reservation details
    echo "<p><strong>Reservation ID:</strong> " . htmlspecialchars($reservationDetails['reservation_id']) . "</p>";
    echo "<p><strong>Reserved By:</strong> " . htmlspecialchars($reservationDetails['reserved_by']) . "</p>";
    echo "<p><strong>Date:</strong> " . htmlspecialchars($reservationDetails['reservation_date']) . "</p>";
    echo "<p><strong>Time:</strong> " . htmlspecialchars($reservationDetails['reservation_time']) . "</p>";
    echo "<p><strong>Table Number:</strong> " . htmlspecialchars($reservationDetails['table_number']) . "</p>";
    echo "<p><strong>Seating Capacity:</strong> " . htmlspecialchars($reservationDetails['seating_capacity']) . "</p>";
    echo "<p><strong>Status:</strong> " . htmlspecialchars($reservationDetails['status']) . "</p>";
}
?>
