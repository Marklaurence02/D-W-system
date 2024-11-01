<?php
session_start();
include_once "../assets/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the required POST data is available
if (isset($_POST['table_id'], $_POST['reservation_date'], $_POST['reservation_time'])) {
    $table_id = intval($_POST['table_id']);
    $reservation_date = $_POST['reservation_date'];
    $reservation_time = $_POST['reservation_time'];
    $custom_note = isset($_POST['custom_note']) ? trim($_POST['custom_note']) : null;

    // Define reservation start and end times
    $start_time = new DateTime("$reservation_date $reservation_time");
    $end_time = (clone $start_time)->modify('+1 hour');

    // Format start and end times for SQL query
    $reservation_time_formatted = $start_time->format('H:i:s');
    $end_time_formatted = $end_time->format('H:i:s');

    // Check for time conflicts
    $conflictCheckQuery = "
        SELECT COUNT(*) AS conflict_count 
        FROM data_reservations 
        WHERE table_id = ? 
          AND reservation_date = ? 
          AND (
                (reservation_time >= ? AND reservation_time < ?) OR 
                (ADDTIME(reservation_time, '01:00:00') > ? AND reservation_time <= ?)
              )";
    $stmt = $conn->prepare($conflictCheckQuery);
    $stmt->bind_param("isssss", $table_id, $reservation_date, $reservation_time_formatted, $end_time_formatted, $reservation_time_formatted, $end_time_formatted);
    $stmt->execute();
    $result = $stmt->get_result();
    $conflict = $result->fetch_assoc();

    if ($conflict['conflict_count'] > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Time slot not available for this table.']);
        exit();
    }

    // Insert reservation into `data_reservations` table without updating table availability
    $insertReservationQuery = "
        INSERT INTO data_reservations (user_id, table_id, reservation_date, reservation_time, custom_note) 
        VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertReservationQuery);
    $stmt->bind_param("iisss", $user_id, $table_id, $reservation_date, $reservation_time_formatted, $custom_note);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Reservation confirmed.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to reserve the table.']);
    }

    $stmt->close();
    $conn->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete reservation data.']);
}
?>
