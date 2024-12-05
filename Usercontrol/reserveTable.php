<?php
session_name("user_session");
session_start();
include_once "../assets/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

// Function to find the first available reservation ID
function findFirstAvailableId($conn) {
    $id = 1; // Start from 1
    
    while (true) {
        // Check both tables for the current ID
        $query = "
            SELECT reservation_id 
            FROM (
                SELECT reservation_id FROM reservations WHERE reservation_id = ?
                UNION
                SELECT reservation_id FROM data_reservations WHERE reservation_id = ?
            ) AS combined_reservations
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // If no results found, this ID is available
        if ($result->num_rows === 0) {
            return $id;
        }
        
        $id++; // Try next ID
    }
}

$user_id = $_SESSION['user_id'];

// Add this function before the main logic
function isReservationIdUnique($conn, $id) {
    $query = "
        SELECT reservation_id 
        FROM (
            SELECT reservation_id FROM reservations WHERE reservation_id = ?
            UNION
            SELECT reservation_id FROM data_reservations WHERE reservation_id = ?
        ) AS combined_reservations
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows === 0;
}

// Check if the required POST data is available
if (isset($_POST['table_id'], $_POST['reservation_date'], $_POST['reservation_time'])) {
    $table_id = intval($_POST['table_id']);
    $reservation_date = $_POST['reservation_date'];
    $reservation_time = $_POST['reservation_time'];
    $custom_note = isset($_POST['custom_note']) ? trim($_POST['custom_note']) : null;

    // Format reservation time for SQL query
    $reservation_time_formatted = (new DateTime("$reservation_date $reservation_time"))->format('H:i:s');

    // Check if the user already has a reservation
    $checkUserReservationQuery = "
        SELECT COUNT(*) AS reservation_count 
        FROM data_reservations 
        WHERE user_id = ?";
    $stmt = $conn->prepare($checkUserReservationQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userReservation = $result->fetch_assoc();

    if ($userReservation['reservation_count'] > 0) {
        echo json_encode(['status' => 'error', 'message' => 'You already have a reservation.']);
        exit();
    }

    // Check for time conflicts for the selected table
    $conflictCheckQuery = "
        SELECT COUNT(*) AS conflict_count 
        FROM data_reservations 
        WHERE table_id = ? 
          AND reservation_date = ? 
          AND reservation_time = ?";
    $stmt = $conn->prepare($conflictCheckQuery);
    $stmt->bind_param("iss", $table_id, $reservation_date, $reservation_time_formatted);
    $stmt->execute();
    $result = $stmt->get_result();
    $conflict = $result->fetch_assoc();

    if ($conflict['conflict_count'] > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Time slot not available for this table.']);
        exit();
    }

    // Add transaction start here
    $conn->begin_transaction();

    // Insert reservation into `data_reservations` table
    $insertReservationQuery = "
        INSERT INTO data_reservations (user_id, table_id, reservation_date, reservation_time, custom_note) 
        VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertReservationQuery);
    $stmt->bind_param("iisss", $user_id, $table_id, $reservation_date, $reservation_time_formatted, $custom_note);

    if ($stmt->execute()) {
        $newReservationId = $stmt->insert_id;
        
        if (!isReservationIdUnique($conn, $newReservationId)) {
            // Instead of rolling back, find a new available ID
            $availableId = findFirstAvailableId($conn);
            
            // Update the reservation with the new ID
            $updateQuery = "UPDATE data_reservations SET reservation_id = ? WHERE reservation_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ii", $availableId, $newReservationId);
            
            if (!$updateStmt->execute()) {
                $conn->rollback();
                echo json_encode(['status' => 'error', 'message' => 'Failed to update reservation ID.']);
                exit();
            }
            $updateStmt->close();
        }
        
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Reservation confirmed.']);
    } else {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to reserve the table.']);
    }

    $stmt->close();
    $conn->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete reservation data.']);
}
?>
