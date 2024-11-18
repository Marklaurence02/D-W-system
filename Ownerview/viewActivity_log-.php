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
        <table id="activityLogsTable" class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">S.N.</th>
                    <th class="text-center">Action By (Role)</th>
                    <th class="text-center">Action Type</th>
                    <th class="text-center">Details</th>
                    <th class="text-center">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = $offset + 1;
                while ($row = $result->fetch_assoc()):
                    $userNameWithRole = htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . " (" . htmlspecialchars($row['role']) . ")";
                ?>
                    <tr>
                        <td class="text-center"><?= $count ?></td>
                        <td class="text-center"><?= $userNameWithRole ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['action_type']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['action_details']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['created_at']) ?></td>
                    </tr>
                <?php $count++; endwhile; ?>
            </tbody>
        </table>


    <?php else: ?>
        <p>No activity logs found.</p>
    <?php endif; ?>
</div>

<!-- Initialize DataTables -->
<script>
$(document).ready(function() {
    // Initialize DataTable with pagination and AJAX
    var table = $('#activityLogsTable').DataTable({
        processing: true,    // Show loading indicator
        serverSide: true,    // Enable server-side processing
        ajax: function(data, callback, settings) {
            // Calculate the page number for the server-side request
            const page = Math.floor(settings.start / settings.length) + 1;
            const searchValue = $('#searchInput').val().trim();
            const actionType = $('#actionTypeSelect').val() || ''; // If you have a select field for action types

            // Fetch data using AJAX
            $.ajax({
                url: "Ownerview/viewActivity_log-.php",
                method: "POST",
                data: {
                    search: searchValue,
                    action: actionType,
                    page: page
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    // Pass the data to DataTable callback
                    callback({
                        draw: settings.draw,
                        recordsTotal: data.total_records,
                        recordsFiltered: data.total_records,
                        data: data.data
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching activity logs:", error);
                    alert("An error occurred while loading activity logs.");
                }
            });
        },
        paging: true,        // Enable pagination
        searching: true,     // Enable search functionality
        ordering: true,      // Enable sorting
        responsive: true,    // Make the table responsive
        pageLength: 10,      // Set default page size
        lengthChange: false, // Disable the page size change option
        language: {
            emptyTable: "No activity logs found"
        }
    });

    // Trigger search when user types in the search input
    $('#searchLog').click(function() {
        table.ajax.reload(); // Reload DataTable data
    });

    // Handle pagination event
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        showActivity_log(searchValue, actionType, page); // Pagination handled by DataTable
    });
});

</script>