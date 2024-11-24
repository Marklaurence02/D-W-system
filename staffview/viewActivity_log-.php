<?php
include_once "../assets/config.php";

session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Fetch all activity logs with user details
$sql = "SELECT activity_logs.*, users.first_name, users.last_name, users.role 
        FROM activity_logs 
        JOIN users ON activity_logs.action_by = users.user_id 
        ORDER BY activity_logs.created_at DESC";
$result = $conn->query($sql);
?>
<div class="container-fluid">

<h2 class="text-center">Activity Logs Report</h2>

<!-- Display Activity Logs Table -->
<div class="allContent-section">
    <table id="activityLogTable" class="table table-striped table-bordered">
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

<!-- Initialize DataTables -->
<script>
   $(document).ready(function() {
        $('#activityLogTable').DataTable({
            "order": [[4, "desc"]], // Order by Timestamp descending
            "pageLength": 10, // Show 10 records per page by default
            "dom": 'Bfrtip', // Add buttons to the DataTables
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    title: 'Activity Logs Report'
                },
                {
                    extend: 'csvHtml5',
                    text: 'Export to CSV',
                    title: 'Activity Logs Report'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Export to PDF',
                    title: 'Activity Logs Report',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: 'Print Report',
                    title: 'Activity Logs Report',
                    customize: function(win) {
                        $(win.document.body).css('font-size', '10pt');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    }
                }
            ]
        });
    });
</script>
