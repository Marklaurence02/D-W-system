<?php
session_name("user_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User  not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Include the database configuration
include '../assets/config.php'; // Adjust the path as necessary

try {
    // Create a new PDO instance using the config
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the query to get the assigned staff ID
    $stmt = $pdo->prepare("SELECT assigned_staff_id FROM user_staff_assignments WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    
    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(['assigned_staff_id' => (int)$result['assigned_staff_id']]);
    } else {
        echo json_encode(['assigned_staff_id' => null]); // No staff assigned
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>