<?php
include_once "../assets/config.php";
session_start();

// Enable error reporting for debugging (remove or set to 0 in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering to prevent any accidental output before JSON response
ob_start();

function findFirstMissingReservationID($conn) {
    $missingIDQuery = "
        SELECT MIN(t1.reservation_id + 1) AS missing_id
        FROM reservations t1
        LEFT JOIN reservations t2 ON t1.reservation_id + 1 = t2.reservation_id
        WHERE t2.reservation_id IS NULL;
    ";
    $result = $conn->query($missingIDQuery);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['missing_id'];
    }
    return null;
}

function handleSuccessfulPayment($conn, $user_id, $totalPayment) {
    $conn->begin_transaction();

    try {
        $firstMissingReservationID = findFirstMissingReservationID($conn);
        
        // Ensure no direct echo statements for debug output
        // Comment out or remove any debug output like below:
        // echo "First missing reservation ID: " . $firstMissingReservationID . "\n";
        // echo "No missing ID found in reservations.\n";

        $updateStatusQuery = "UPDATE data_reservations SET status = 'Paid' WHERE user_id = ? AND status = 'Pending'";
        $stmt = $conn->prepare($updateStatusQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $transferReservationsQuery = "
            INSERT INTO reservations (user_id, table_id, reservation_date, reservation_time, status, custom_note, feedback, created_at, updated_at)
            SELECT user_id, table_id, reservation_date, reservation_time, status, custom_note, feedback, created_at, updated_at
            FROM data_reservations
            WHERE user_id = ? AND status = 'Paid'
        ";
        $stmt = $conn->prepare($transferReservationsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $transferOrdersQuery = "
            INSERT INTO orders (user_id, reservation_id, order_details, total_amount, order_time, status, created_at, updated_at, payment_method)
            SELECT 
                oi.user_id,
                dr.reservation_id,
                GROUP_CONCAT(CONCAT('Product ID: ', oi.product_id, ' | Quantity: ', oi.quantity, ' | Price: ', oi.totalprice) SEPARATOR '; ') AS order_details,
                ?,
                NOW(),
                'paid in advance',
                NOW(),
                NOW(),
                'Credit Card'
            FROM order_items oi
            LEFT JOIN data_reservations dr ON oi.user_id = dr.user_id
            WHERE oi.user_id = ?
            GROUP BY oi.order_id
        ";
        $stmt = $conn->prepare($transferOrdersQuery);
        $stmt->bind_param("di", $totalPayment, $user_id);
        $stmt->execute();
        $stmt->close();

        $receiptQuery = "
            INSERT INTO receipts (order_id, user_id, total_amount, payment_method)
            SELECT DISTINCT o.order_id, o.user_id, ?, 'Credit Card'
            FROM orders o
            WHERE o.user_id = ?
        ";
        $stmt = $conn->prepare($receiptQuery);
        $stmt->bind_param("di", $totalPayment, $user_id);
        $stmt->execute();
        $receipt_id = $stmt->insert_id;
        $stmt->close();

        $receiptItemsQuery = "
            INSERT INTO receipt_items (receipt_id, product_id, quantity, item_total_price)
            SELECT ?, oi.product_id, oi.quantity, oi.totalprice
            FROM order_items oi
            WHERE oi.user_id = ?
        ";
        $stmt = $conn->prepare($receiptItemsQuery);
        $stmt->bind_param("ii", $receipt_id, $user_id);
        $stmt->execute();
        $stmt->close();

        $clearOrderItemsQuery = "DELETE FROM order_items WHERE user_id = ?";
        $stmt = $conn->prepare($clearOrderItemsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $clearReservationsQuery = "DELETE FROM data_reservations WHERE user_id = ? AND status = 'Paid'";
        $stmt = $conn->prepare($clearReservationsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        ob_end_clean(); // Clean the buffer to ensure only JSON is sent
        echo json_encode(['status' => 'success', 'message' => 'Payment processed, status updated, and data transferred successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        ob_end_clean(); // Clean the buffer to ensure only JSON is sent
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
}

if (isset($_POST['user_id']) && isset($_POST['totalPayment'])) {
    $user_id = intval($_POST['user_id']);
    $totalPayment = floatval($_POST['totalPayment']);

    if ($user_id > 0 && $totalPayment > 0) {
        handleSuccessfulPayment($conn, $user_id, $totalPayment);
    } else {
        ob_end_clean(); // Clean the buffer to ensure only JSON is sent
        echo json_encode(['status' => 'error', 'message' => 'Invalid data provided.']);
    }
} else {
    ob_end_clean(); // Clean the buffer to ensure only JSON is sent
    echo json_encode(['status' => 'error', 'message' => 'Required data missing.']);
}

$conn->close();
