<?php 
session_start();
include_once "../assets/config.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true); // Decode JSON input
$reservation_id = isset($data['reservation_id']) ? intval($data['reservation_id']) : null;

// Log incoming data for debugging
error_log("User ID: $user_id, Received Reservation ID: " . json_encode($reservation_id));

if (!$reservation_id) {
    echo json_encode(['status' => 'error', 'message' => 'Reservation ID is required and must be valid.']);
    exit();
}

try {
    $query = "DELETE FROM data_reservations WHERE reservation_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $reservation_id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Reservation deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No matching reservation found to delete or insufficient permissions.']);
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Error while deleting reservation: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>
