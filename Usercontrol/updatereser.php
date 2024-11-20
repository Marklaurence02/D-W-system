<?php
session_name("user_session");
session_start();
include_once "../assets/config.php";

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data and sanitize inputs
    $reservationId = $_POST['reservationId'] ?? null;
    $editDate = $_POST['editDate'] ?? null;
    $editTime = $_POST['editTime'] ?? null;
    $editNote = $_POST['editNote'] ?? null;

    // Check if necessary data is provided
    if (!$reservationId || !$editDate || !$editTime) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit();
    }

    // Begin transaction to ensure both updates happen together
    $conn->begin_transaction();

    try {
        // First update query to update reservation details
        $query = "
            UPDATE reservations
            SET reservation_date = ?, reservation_time = ?, custom_note = ?, updated_at = NOW()
            WHERE reservation_id = ? AND status = 'Rescheduled'
        ";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("sssi", $editDate, $editTime, $editNote, $reservationId);

            if (!$stmt->execute()) {
                throw new Exception('Failed to update reservation details');
            }

            $stmt->close();
        } else {
            throw new Exception('Database error while preparing update query');
        }

        // Second update query to change the status to 'Paid'
        $statusQuery = "
            UPDATE reservations
            SET status = 'Paid', updated_at = NOW()
            WHERE reservation_id = ? AND status = 'Rescheduled'
        ";

        if ($stmt = $conn->prepare($statusQuery)) {
            $stmt->bind_param("i", $reservationId);

            if (!$stmt->execute()) {
                throw new Exception('Failed to update reservation status');
            }

            $stmt->close();
        } else {
            throw new Exception('Database error while preparing status update query');
        }

        // Commit the transaction
        $conn->commit();

        // Return success message
        echo json_encode(['status' => 'success', 'message' => 'Reservation updated and status set to Paid']);
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn->rollback();

        // Return error message
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

// Close the database connection
$conn->close();
?>
