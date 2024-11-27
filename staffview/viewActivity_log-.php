<?php
include_once "../assets/config.php";

    session_start();


// Fetch all activity logs with user details
$sql = "SELECT activity_logs.*, users.first_name, users.last_name, users.role 
        FROM activity_logs 
        JOIN users ON activity_logs.action_by = users.user_id 
        ORDER BY activity_logs.created_at DESC";
$result = $conn->query($sql);
?>
<div class="container-fluid p-3">
    <div class="col-12">
        <div class="text-center mb-3">
            <h2>Activity Logs Report</h2>
        </div>

        <!-- Activity Logs Table -->
        <div class="table-responsive">
            <table id="activityLogTable" class="table table-striped table-hover table-bordered display nowrap">
                <thead>
                    <tr>
                        <th class='text-center'>O.N.</th>
                        <th class='text-center'>Action By (Role)</th>
                        <th class='text-center'>Action Type</th>
                        <th class='text-center'>Details</th>
                        <th class='text-center'>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0):
                        $count = 1;
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
                    <?php 
                        $count++;
                        endwhile; 
                    endif; 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable with new styling
    $('#activityLogTable').DataTable({
        responsive: true,
        autoWidth: false,
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search activity logs..."
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success',
                titleAttr: 'Export to Excel',
                title: 'Activity Logs Report'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info',
                titleAttr: 'Export to CSV',
                title: 'Activity Logs Report'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger',
                titleAttr: 'Export to PDF',
                title: 'Activity Logs Report',
                orientation: 'landscape',
                pageSize: 'A4'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-primary',
                titleAttr: 'Print Table',
                title: 'Activity Logs Report',
                autoPrint: true
            }
        ],
        order: [[4, "desc"]] // Order by Timestamp descending
    });
});
</script>

<style>
/* Copy all styles from viewAllOrders.php and adjust as needed */
.dataTables_wrapper {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

/* Search Box Styling */
.dataTables_filter {
    position: relative;
    margin-bottom: 20px;
}

.dataTables_filter label {
    display: flex;
    align-items: center;
    margin: 0;
    width: 100%;
    position: relative;
}

.dataTables_filter input {
    width: 300px !important;
    height: 40px;
    padding: 8px 16px 8px 40px !important;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    font-size: 14px;
    transition: all 0.3s ease;
    margin: 0 !important;
}

/* ... continue with other relevant styles from viewAllOrders.php ... */

/* Specific Column Widths for Activity Log */
#activityLogTable th:nth-child(1), /* O.N. */
#activityLogTable td:nth-child(1) {
    width: 5%;
    min-width: 50px;
}

#activityLogTable th:nth-child(2), /* Action By */
#activityLogTable td:nth-child(2) {
    width: 20%;
    min-width: 150px;
}

#activityLogTable th:nth-child(3), /* Action Type */
#activityLogTable td:nth-child(3) {
    width: 15%;
    min-width: 120px;
}

#activityLogTable th:nth-child(4), /* Details */
#activityLogTable td:nth-child(4) {
    width: 40%;
    min-width: 200px;
}

#activityLogTable th:nth-child(5), /* Timestamp */
#activityLogTable td:nth-child(5) {
    width: 20%;
    min-width: 150px;
}
</style>
