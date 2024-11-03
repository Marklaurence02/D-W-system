<?php
include 'db_connection.php';
header('Content-Type: application/json');

$result = $conn->query("SELECT username FROM users");
$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = ['username' => $row['username']];
}

echo json_encode($users);
?>
