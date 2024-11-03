<?php
session_start();
include_once "../assets/config.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
error_log("Received data: " . print_r($data, true));

// Fallback to raw POST data if JSON decoding fails
if (!$data) {
    $data = $_POST;
}

if (!isset($data['reservation_id']) || !isset($data['table_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Reservation ID and Table ID are required.']);
    exit();
}

$reservation_id = intval($data['reservation_id']);
$table_id = intval($data['table_id']);
$reservation_date = !empty($data['reservation_date']) ? $data['reservation_date'] : null;
$reservation_time = !empty($data['reservation_time']) ? $data['reservation_time'] : null;
$custom_note = isset($data['custom_note']) ? $data['custom_note'] : null;

$query = "SELECT table_id, reservation_date, reservation_time, custom_note FROM data_reservations WHERE reservation_id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $reservation_id, $user_id);
$stmt->execute();
$currentData = $stmt->get_result()->fetch_assoc();
$stmt->close();

$changesMade = (
    $currentData['table_id'] != $table_id ||
    $currentData['reservation_date'] != $reservation_date ||
    $currentData['reservation_time'] != $reservation_time ||
    $currentData['custom_note'] != $custom_note
);

if (!$changesMade) {
    echo json_encode(['status' => 'info', 'message' => 'No changes were made.']);
    exit();
}

try {
    if ($reservation_time) {
        $query = "UPDATE data_reservations 
                  SET table_id = ?, 
                      reservation_date = COALESCE(?, reservation_date), 
                      reservation_time = COALESCE(?, reservation_time), 
                      custom_note = ?, 
                      updated_at = CURRENT_TIMESTAMP
                  WHERE reservation_id = ? AND user_id = ? AND status = 'Pending'";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issisi", $table_id, $reservation_date, $reservation_time, $custom_note, $reservation_id, $user_id);
    } else {
        $query = "UPDATE data_reservations 
                  SET table_id = ?, 
                      reservation_date = COALESCE(?, reservation_date), 
                      custom_note = ?, 
                      updated_at = CURRENT_TIMESTAMP
                  WHERE reservation_id = ? AND user_id = ? AND status = 'Pending'";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issii", $table_id, $reservation_date, $custom_note, $reservation_id, $user_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => ($stmt->affected_rows > 0 ? 'success' : 'info'), 'message' => ($stmt->affected_rows > 0 ? 'Reservation updated successfully.' : 'No changes made or reservation not found.')]);
    } else {
        error_log("Execution error: " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Failed to execute the update query.']);
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while updating the reservation.']);
}

$conn->close();

?>
