<?php
include_once "../assets/config.php"; // Include your database connection

// Get DataTable parameters
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$search = $_POST['search']['value'] ?? '';
$order_column = $_POST['order'][0]['column'] ?? 1; // Default to the first sortable column
$order_dir = $_POST['order'][0]['dir'] ?? 'asc';   // Default order direction
$columns = ['user_id', 'username', 'email', 'contact_number', 'created_at']; // Columns to sort by

// Validate order column
$order_column = isset($columns[$order_column]) ? $columns[$order_column] : 'username';

// Base query
$where = "role = 'General User'";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $where .= " AND (username LIKE '%$search%' OR email LIKE '%$search%' OR contact_number LIKE '%$search%')";
}

// Count total and filtered records
$totalQuery = "SELECT COUNT(*) as total FROM users WHERE role = 'General User'";
$totalRecords = $conn->query($totalQuery)->fetch_assoc()['total'];

$filteredQuery = "SELECT COUNT(*) as total FROM users WHERE $where";
$totalFiltered = $conn->query($filteredQuery)->fetch_assoc()['total'];

// Fetch data
$dataQuery = "SELECT user_id, username, email, contact_number, created_at
              FROM users 
              WHERE $where 
              ORDER BY $order_column $order_dir 
              LIMIT $start, $length";
$dataResult = $conn->query($dataQuery);

$data = [];
$sn = $start + 1;
while ($row = $dataResult->fetch_assoc()) {
    $row['sn'] = $sn++;
    $data[] = $row;
}

// Return JSON response
echo json_encode([
    "draw" => intval($_POST['draw'] ?? 1),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
]);
