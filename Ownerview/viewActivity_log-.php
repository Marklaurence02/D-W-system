<?php
include_once "../assets/config.php";

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}



// Get the search term sent from JavaScript (if any)
$search = isset($_POST['search']) ? $conn->real_escape_string($_POST['search']) : '';

// SQL query to fetch activity logs, joining with users to get their role
$sql = "SELECT activity_logs.*, users.first_name, users.last_name, users.role 
        FROM activity_logs 
        JOIN users ON activity_logs.action_by = users.user_id";

// Modify SQL query to filter results based on the search term
if (!empty($search)) {
    $sql .= " WHERE (activity_logs.action_type LIKE '%$search%' 
              OR activity_logs.action_details LIKE '%$search%' 
              OR users.first_name LIKE '%$search%' 
              OR users.last_name LIKE '%$search%' 
              OR users.role LIKE '%$search%')";
}

$result = $conn->query($sql);
$count = 1;

if ($result->num_rows > 0) {
    echo "<table class='table'>
            <thead>
                <tr>
                    <th class='text-center'>S.N.</th>
                    <th class='text-center'>Action By (Role)</th>
                    <th class='text-center'>Action Type</th>
                    <th class='text-center'>Details</th>
                    <th class='text-center'>Timestamp</th>
                </tr>
            </thead>";
    
    while ($row = $result->fetch_assoc()) {
        // Combine user name and role
        $userNameWithRole = $row['first_name'] . " " . $row['last_name'] . " (" . $row['role'] . ")";

        echo "<tr>
                <td class='text-center'>{$count}</td>
                <td class='text-center'>{$userNameWithRole}</td>
                <td class='text-center'>{$row['action_type']}</td>
                <td class='text-center'>{$row['action_details']}</td>
                <td class='text-center'>{$row['created_at']}</td>
              </tr>";
        $count++;
    }

    echo "</table>";
} else {
    echo "<p>No activity logs found.</p>";
}
