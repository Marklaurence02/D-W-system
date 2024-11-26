<?php
session_name("user_session");
session_start();
include_once "../assets/config.php";

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the user has any orders
$orderQuery = "SELECT COUNT(*) AS order_count FROM order_items WHERE user_id = ?";
$stmt = $conn->prepare($orderQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($order_count);
$stmt->fetch();
$stmt->close();

$has_orders = $order_count > 0;

// Check if the user has any table reservations
$reservationQuery = "SELECT COUNT(*) AS reservation_count FROM data_reservations WHERE user_id = ?";
$stmt = $conn->prepare($reservationQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($reservation_count);
$stmt->fetch();
$stmt->close();

$has_reservations = $reservation_count > 0;

// Fixed percentages for progress bar
$order_percentage = 30;
$reservation_percentage = 30;
$other_percentage = 40; // Assuming this is for some other category

// Return JSON response
echo json_encode([
    'status' => 'success',
    'has_orders' => $has_orders,
    'has_reservations' => $has_reservations,
    'order_percentage' => $order_percentage,
    'reservation_percentage' => $reservation_percentage,
    'other_percentage' => $other_percentage
]);
?>
