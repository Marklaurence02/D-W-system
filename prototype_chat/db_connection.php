<?php
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your password
$dbname = "live_chat_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
