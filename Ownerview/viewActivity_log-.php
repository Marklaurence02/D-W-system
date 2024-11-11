<?php
include_once "../assets/config.php";

session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch parameters with fallback values
$search = isset($_POST['search']) ? $conn->real_escape_string($_POST['search']) : '';
$action = isset($_POST['action']) ? $conn->real_escape_string($_POST['action']) : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

$limit = 10;
$offset = ($page - 1) * $limit;

// Main query to fetch filtered activity logs with pagination
$sql = "SELECT activity_logs.*, users.first_name, users.last_name, users.role 
        FROM activity_logs 
        JOIN users ON activity_logs.action_by = users.user_id 
        WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (activity_logs.action_type LIKE '%$search%' 
                  OR activity_logs.action_details LIKE '%$search%' 
                  OR users.first_name LIKE '%$search%' 
                  OR users.last_name LIKE '%$search%' 
                  OR users.role LIKE '%$search%')";
}

if (!empty($action)) {
    $sql .= " AND activity_logs.action_type = '$action'";
}

$sql .= " ORDER BY activity_logs.created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Count query for pagination
$count_sql = "SELECT COUNT(*) as total 
              FROM activity_logs 
              JOIN users ON activity_logs.action_by = users.user_id 
              WHERE 1=1";

if (!empty($search)) {
    $count_sql .= " AND (activity_logs.action_type LIKE '%$search%' 
                     OR activity_logs.action_details LIKE '%$search%' 
                     OR users.first_name LIKE '%$search%' 
                     OR users.last_name LIKE '%$search%' 
                     OR users.role LIKE '%$search%')";
}

if (!empty($action)) {
    $count_sql .= " AND activity_logs.action_type = '$action'";
}

$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);
?>
<!-- search -->
<div class="search-bar">
    <div class="col-12 col-md-4 mb-2 mb-md-0 d-flex align-items-center">
        <input type="text" id="searchInput" class="form-control" placeholder="Search by username, email, or contact number">
        <button id="searchLog" class="btn btn-primary ml-2" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.4rem; width: 2.5rem; height: 2.5rem;">
            <i class="fa fa-search" aria-hidden="true"></i>
        </button>
    </div>
</div>

<!-- Display Activity Logs -->
<div class="allContent-section">
    <?php if ($result->num_rows > 0): ?>
        <table class='table'>
            <thead>
                <tr>
                    <th class='text-center'>S.N.</th>
                    <th class='text-center'>Action By (Role)</th>
                    <th class='text-center'>Action Type</th>
                    <th class='text-center'>Details</th>
                    <th class='text-center'>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = $offset + 1;
                while ($row = $result->fetch_assoc()):
                    $userNameWithRole = htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . " (" . htmlspecialchars($row['role']) . ")";
                ?>
                    <tr>
                        <td class='text-center'><?= $count ?></td>
                        <td class='text-center'><?= $userNameWithRole ?></td>
                        <td class='text-center'><?= htmlspecialchars($row['action_type']) ?></td>
                        <td class='text-center'><?= htmlspecialchars($row['action_details']) ?></td>
                        <td class='text-center'><?= htmlspecialchars($row['created_at']) ?></td>
                    </tr>
                <?php $count++; endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class='pagination'>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href='#' class='page-link <?= $i == $page ? "active-page" : "" ?>' data-page='<?= $i ?>'><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php else: ?>
        <p>No activity logs found.</p>
    <?php endif; ?>
</div>
