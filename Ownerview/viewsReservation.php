<?php
session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once "../assets/config.php";

// Handle form submission to change reservation status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reservation_id']) && isset($_POST['status'])) {
    $reservationId = intval($_POST['reservation_id']);
    $status = $_POST['status'];

    $updateSql = "UPDATE reservations SET status = ?, updated_at = NOW() WHERE reservation_id = ?";
    $stmt = $conn->prepare($updateSql);

    if (!$stmt) {
        error_log("SQL Prepare Error: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => "SQL error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("si", $status, $reservationId);

    if ($stmt->execute()) {
        $message = "Reservation status updated successfully.";
        if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
            echo json_encode(['status' => 'success', 'message' => $message]);
            exit;
        }
    } else {
        $message = "Error updating reservation status: " . htmlspecialchars($stmt->error);
        error_log("SQL Execute Error: " . $stmt->error);
        if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
            echo json_encode(['status' => 'error', 'message' => $message]);
            exit;
        }
    }

    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($message));
    exit;
}

// Fetch reservations for display
$sql = "
  SELECT r.*, t.table_number, t.seating_capacity, 
         CONCAT(u.first_name, ' ', u.last_name) AS reserved_by
  FROM reservations r
  LEFT JOIN tables t ON r.table_id = t.table_id
  LEFT JOIN users u ON r.user_id = u.user_id";
$result = $conn->query($sql);
$count = 1;

if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>

<div class="mb-2">
    <label for="filter_status">Filter by Status: </label>
    <select id="filter_status" class="form-control d-inline-block" style="width: 200px;">
        <option value="All">All</option>
        <option value="Pending">Pending</option>
        <option value="Confirmed">Confirmed</option>
        <option value="Canceled">Canceled</option>
        <option value="Rescheduled">Rescheduled</option>
        <option value="Paid">Paid</option>
    </select>
</div>

<!-- Table view (visible on larger screens) -->
<div class="table-responsive d-none d-md-block">
    <table id="reservationTable" class="table table-striped">
        <thead>
            <tr>
                <th class="text-center">S.N.</th>
                <th class="text-center">Reserved By</th>
                <th class="text-center">Reservation Date</th>
                <th class="text-center">Reservation Time</th>
                <th class="text-center">Table Number</th>
                <th class="text-center">Seating Capacity</th>
                <th class="text-center">Status</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="table_body">
            <?php
            if ($result && $result->num_rows > 0) {
                $count = 1;
                while ($row = $result->fetch_assoc()) {
            ?>
            <tr>
                <td class="text-center"><?= $count ?></td>
                <td class="text-center"><?= htmlspecialchars($row["reserved_by"]) ?></td>
                <td class="text-center"><?= date("F j, Y", strtotime($row["reservation_date"])) ?></td>
                <td class="text-center"><?= date("g:i A", strtotime($row["reservation_time"])) ?></td>
                <td class="text-center"><?= htmlspecialchars($row["table_number"]) ?></td>
                <td class="text-center"><?= htmlspecialchars($row["seating_capacity"]) ?></td>
                <td class="text-center">
                    <span class="badge 
                        <?= ($row['status'] == 'Pending') ? 'badge-warning' : 
                            (($row['status'] == 'Confirmed') ? 'badge-success' : 
                            (($row['status'] == 'Canceled') ? 'badge-danger' : 
                            (($row['status'] == 'Rescheduled') ? 'badge-info' : 'badge-primary'))) ?>">
                      <?= htmlspecialchars($row["status"]) ?>
                    </span>
                </td>
                <td class="text-center">
                    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="reservation-form">
                        <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($row['reservation_id']) ?>">
                        <input type="hidden" name="ajax" value="1">
                        <div class="d-flex flex-column align-items-center">
                            <select name="status" class="form-control mb-2">
                                <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Confirmed" <?= $row['status'] == 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="Canceled" <?= $row['status'] == 'Canceled' ? 'selected' : '' ?>>Canceled</option>
                                <option value="Rescheduled" <?= $row['status'] == 'Rescheduled' ? 'selected' : '' ?>>Rescheduled</option>
                                <option value="Paid" <?= $row['status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </td>
            </tr>
            <?php
                $count++;
                }
            } else {
                echo "<tr><td colspan='8' class='text-center'>No reservations found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>



<!-- Modal HTML -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Status</h5>
        <button type="button" class="close" onclick="closeModal()" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- Success or error message will be injected here -->
      </div>
      <div class="modal-footer">
        <!-- Use a custom function for closing the modal -->
        <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
      </div>
    </div>
  </div>
</div>




<?php
$conn->close();
?>

<script>
$(document).ready(function () {
    // Initialize DataTable
    var table = $('#reservationTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        columnDefs: [
            { orderable: false, targets: [7] } // Disable ordering for the Actions column
        ]
    });

    // Add event listener to the "Filter by Status" dropdown
    $('#filter_status').on('change', function () {
        var selectedValue = $(this).val();

        if (selectedValue === 'All') {
            // Reset the filter when "All" is selected
            table.column(6).search('').draw();  // 6 is the index of the "Status" column
        } else {
            // Filter rows by the selected status value
            table.column(6).search(selectedValue).draw();
        }
    });
});



</script>