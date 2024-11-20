<?php
session_name("user_session");
session_start();
include_once "../assets/config.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to provide feedback.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve the feedback data from the POST request
$reservationId = isset($_POST['reservationId']) ? $_POST['reservationId'] : null;
$feedbackText = isset($_POST['feedbackText']) ? trim($_POST['feedbackText']) : '';
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

// Validate input
if (empty($reservationId) || empty($feedbackText) || $rating === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide feedback and a rating.']);
    exit();
}

// Sanitize inputs
$feedbackText = htmlspecialchars($feedbackText, ENT_QUOTES, 'UTF-8');

// Prepare the SQL query to insert the feedback
$query = "INSERT INTO feedbacks (reservation_id, user_id, feedback_text, rating, created_at) 
          VALUES (?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($query);
$stmt->bind_param("iisi", $reservationId, $user_id, $feedbackText, $rating);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Feedback submitted successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to submit feedback. Please try again later.']);
}

$stmt->close();
$conn->close();
?>
