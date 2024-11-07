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
        // Update the status of data_reservations
        $updateStatusQuery = "UPDATE data_reservations SET status = 'Paid' WHERE user_id = ? AND status = 'Pending'";
        $stmt = $conn->prepare($updateStatusQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Transfer reservations to the reservations table and keep track of the original reservation_id
        $transferReservationsQuery = "
            INSERT INTO reservations (reservation_id, user_id, table_id, reservation_date, reservation_time, status, custom_note, feedback, created_at, updated_at)
            SELECT reservation_id, user_id, table_id, reservation_date, reservation_time, status, custom_note, feedback, created_at, updated_at
            FROM data_reservations
            WHERE user_id = ? AND status = 'Paid'
        ";
        $stmt = $conn->prepare($transferReservationsQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Transfer order details to the orders table using reservation_id from data_reservations
       // Transfer order details to the orders table using reservation_id from data_reservations
$transferOrdersQuery = "
INSERT INTO orders (user_id, reservation_id, order_details, total_amount, order_time, status, created_at, updated_at, payment_method)
SELECT 
    oi.user_id,
    dr.reservation_id,
    GROUP_CONCAT(
        CONCAT(
            'Product Name: ', pi.product_name, ' | Quantity: ', oi.quantity, ' | Price: ', oi.totalprice
        ) SEPARATOR '; '
    ) AS order_details,
    ?,
    NOW(),
    'paid in advance',
    NOW(),
    NOW(),
    'Credit Card'
FROM order_items oi
JOIN data_reservations dr ON oi.user_id = dr.user_id AND dr.status = 'Paid'
JOIN product_items pi ON oi.product_id = pi.product_id
WHERE oi.user_id = ?
GROUP BY dr.reservation_id
";
$stmt = $conn->prepare($transferOrdersQuery);
$stmt->bind_param("di", $totalPayment, $user_id);
$stmt->execute();
$stmt->close();


        // Insert into the receipts table and get the new receipt_id
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

        // Insert into receipt_items with reservation_id from data_reservations
        $receiptItemsQuery = "
            INSERT INTO receipt_items (receipt_id, reservation_id, product_id, quantity, item_total_price, user_id)
            SELECT ?, dr.reservation_id, oi.product_id, oi.quantity, oi.totalprice, oi.user_id
            FROM order_items oi
            JOIN data_reservations dr ON oi.user_id = dr.user_id AND dr.status = 'Paid'
            WHERE oi.user_id = ?
        ";
        $stmt = $conn->prepare($receiptItemsQuery);
        $stmt->bind_param("ii", $receipt_id, $user_id);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            throw new Exception("No receipt items were inserted.");
        }
        $stmt->close();

        // Clear order_items and data_reservations after successful insertion
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
