<?php
session_start();
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    include('config.php'); // Database connection

    // Fetch user data
    $sql = "SELECT first_name, middle_initial, last_name, contact_number, email, address, zip_code, username FROM users WHERE user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            echo json_encode($user); // Return the user data as JSON
        } else {
            echo json_encode(["error" => "User not found"]);
        }
    } else {
        echo json_encode(["error" => "Database query failed"]);
    }

    $conn->close();
}
?>
