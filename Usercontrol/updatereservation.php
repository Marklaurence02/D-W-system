<?php
session_name("user_session");
session_start();
include_once "../assets/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

// Retrieve and sanitize input data
$reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
$tableId = filter_input(INPUT_POST, 'table_id', FILTER_VALIDATE_INT);
$reservationDate = filter_input(INPUT_POST, 'reservation_date', FILTER_SANITIZE_STRING);
$reservationTime = filter_input(INPUT_POST, 'reservation_time', FILTER_SANITIZE_STRING);
$customNote = filter_input(INPUT_POST, 'custom_note', FILTER_SANITIZE_STRING);

if (!$reservationId || !$tableId || !$reservationDate || !$reservationTime) {
    echo json_encode(['status' => 'error', 'message' => 'Missing or invalid input data.']);
    exit();
}

// Prepare the SQL statement
$query = "
    UPDATE data_reservations
    SET table_id = ?, reservation_date = ?, reservation_time = ?, custom_note = ?, updated_at = NOW()
    WHERE reservation_id = ? AND user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("isssii", $tableId, $reservationDate, $reservationTime, $customNote, $reservationId, $_SESSION['user_id']);

// Execute the query and check for success
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Reservation updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update reservation.']);
}

$stmt->close();
$conn->close();
?>
