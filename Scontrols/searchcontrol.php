<?php
include_once "../assets/config.php";

// Pagination logic
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1; // Default to 1 if page is invalid
}
$offset = ($page - 1) * $limit;

// Retrieve the filter inputs (if provided)
$search = isset($_GET['search']) ? htmlspecialchars($conn->real_escape_string(trim($_GET['search']))) : '';
$startDate = isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : '';
$endDate = isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : '';

// Construct the SQL query to show all users by default or filter if conditions are provided
$sql = "SELECT * FROM users WHERE role = 'General User'";

// Search conditions
if (!empty($search)) {
    // Split search term into words for better matching on first_name and last_name
    $searchTerms = explode(' ', $search);
    $searchConditions = [];

    // Add a condition to search by full name (concatenated first_name and last_name)
    $fullNameSearch = $conn->real_escape_string($search);
    $searchConditions[] = "CONCAT(first_name, ' ', last_name) LIKE '%$fullNameSearch%'";

    // Add conditions for first_name, last_name, email, and contact_number for each term
    foreach ($searchTerms as $term) {
        $term = $conn->real_escape_string($term);
        $searchConditions[] = "(first_name LIKE '%$term%' 
                                OR last_name LIKE '%$term%' 
                                OR email LIKE '%$term%' 
                                OR contact_number LIKE '%$term%')";
    }

    // Combine all conditions with AND
    $sql .= " AND (" . implode(' AND ', $searchConditions) . ")";
}

// Date range filter if provided
if (!empty($startDate) && !empty($endDate)) {
    $sql .= " AND DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
} elseif (!empty($startDate)) {
    $sql .= " AND DATE(created_at) >= '$startDate'";
} elseif (!empty($endDate)) {
    $sql .= " AND DATE(created_at) <= '$endDate'";
}

// Count total users for pagination with filters
$total_sql = "SELECT COUNT(*) FROM users WHERE role='General User'";

if (!empty($search)) {
    $total_sql .= " AND (" . implode(' AND ', $searchConditions) . ")";
}

if (!empty($startDate) && !empty($endDate)) {
    $total_sql .= " AND DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
} elseif (!empty($startDate)) {
    $total_sql .= " AND DATE(created_at) >= '$startDate'";
} elseif (!empty($endDate)) {
    $total_sql .= " AND DATE(created_at) <= '$endDate'";
}

// Execute total count query for pagination
$total_result = $conn->query($total_sql);
$total_users = $total_result ? $total_result->fetch_array()[0] : 0;
$total_pages = ceil($total_users / $limit);

// Append LIMIT and OFFSET for pagination
$sql .= " LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Output the results (either all or filtered)
if ($result && $result->num_rows > 0) {
    $count = $offset + 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td class='text-center'>{$count}</td>
                <td class='text-center'>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</td>
                <td class='text-center'>" . htmlspecialchars($row['email']) . "</td>
                <td class='text-center'>" . htmlspecialchars($row['contact_number']) . "</td>
                <td class='text-center'>" . htmlspecialchars($row['created_at']) . "</td>
              </tr>";
        $count++;
    }
} else {
    echo '<tr><td colspan="5" class="text-center">No users found.</td></tr>';
}

// Pagination controls
if ($total_pages > 1) {
    echo '<tr><td colspan="5"><nav><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($page > 1) {
        echo '<li class="page-item"><a class="page-link" href="#" onclick="UfilterItems(' . ($page - 1) . ')">&laquo;</a></li>';
    }

    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        $activeClass = ($i == $page) ? 'active' : '';
        echo '<li class="page-item ' . $activeClass . '"><a class="page-link" href="#" onclick="UfilterItems(' . $i . ')">' . $i . '</a></li>';
    }

    // Next button
    if ($page < $total_pages) {
        echo '<li class="page-item"><a class="page-link" href="#" onclick="UfilterItems(' . ($page + 1) . ')">&raquo;</a></li>';
    }

    echo '</ul></nav></td></tr>';
}
?>
