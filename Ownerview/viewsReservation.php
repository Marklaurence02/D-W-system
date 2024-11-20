<?php
session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once "../assets/config.php";

// Handle form submission to update reservation status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reservation_id'], $_POST['status'])) {
    $reservationId = intval($_POST['reservation_id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE reservations SET status = ?, updated_at = NOW() WHERE reservation_id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $status, $reservationId);
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Reservation status updated successfully.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Error updating reservation status: ' . $stmt->error];
        }
        $stmt->close();
    } else {
        $response = ['status' => 'error', 'message' => 'SQL error: ' . $conn->error];
    }

    if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
        echo json_encode($response);
        exit;
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($response['message']));
    exit;
}

// Fetch reservations
$sql = "
  SELECT r.reservation_id, r.status, r.reservation_date, r.reservation_time, 
         t.table_number, t.seating_capacity, 
         CONCAT(u.first_name, ' ', u.last_name) AS reserved_by
  FROM reservations r
  LEFT JOIN tables t ON r.table_id = t.table_id
  LEFT JOIN users u ON r.user_id = u.user_id";
$reservations = $conn->query($sql);

$message = $_GET['message'] ?? null;
?>



<div class="mb-2">
    <label for="filter_status">Filter by Status:</label>
    <select id="filter_status" class="form-select d-inline-block" style="width: 200px;">
        <option value="All">All</option>
        <option value="Pending">Pending</option>
        <option value="Complete">Complete</option>
        <option value="Canceled">Canceled</option>
        <option value="Rescheduled">Rescheduled</option>
        <option value="Paid">Paid</option>
    </select>
</div>

<div class="table-responsive">
    <table id="reservationTable" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Reserved By</th>
                <th>Date</th>
                <th>Time</th>
                <th>Table</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($reservations && $reservations->num_rows > 0): ?>
                <?php $count = 1; while ($row = $reservations->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['reserved_by']) ?></td>
                        <td><?= date("F j, Y", strtotime($row['reservation_date'])) ?></td>
                        <td><?= date("g:i A", strtotime($row['reservation_time'])) ?></td>
                        <td><?= htmlspecialchars($row['table_number']) ?></td>
                        <td><?= htmlspecialchars($row['seating_capacity']) ?></td>
                        <td>
                            <span class="badge bg-<?= match ($row['status']) {
                                'Pending' => 'warning',
                                'Complete' => 'success',
                                'Canceled' => 'danger',
                                'Rescheduled' => 'info',
                                default => 'primary'
                            } ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="reservation-form">
                                <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($row['reservation_id']) ?>">
                                <input type="hidden" name="ajax" value="1">
                                <select name="status" class="form-select mb-2">
                                    <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Complete" <?= $row['status'] === 'Complete' ? 'selected' : '' ?>>Complete</option>
                                    <option value="Canceled" <?= $row['status'] === 'Canceled' ? 'selected' : '' ?>>Canceled</option>
                                    <option value="Rescheduled" <?= $row['status'] === 'Rescheduled' ? 'selected' : '' ?>>Rescheduled</option>
                                    <option value="Paid" <?= $row['status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center">No reservations found.</td></tr>
            <?php endif; ?>
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
$(document).ready(function() {
    // Initialize DataTable with Bootstrap 5 styling
    const table = $('#reservationTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', title: 'Reservation Report' },
            { extend: 'csvHtml5', title: 'Reservation Report' },
            { extend: 'pdfHtml5', title: 'Reservation Report', orientation: 'landscape', pageSize: 'A4' },
            { extend: 'print', title: 'Reservation Report' }
        ],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[2, 'desc']],
        responsive: true
    });

    // Filter table by status
    $('#filter_status').on('change', function() {
        const status = $(this).val();
        table.column(6).search(status === "All" ? "" : status).draw();
    });

    // Handle form submission via AJAX
    $('.reservation-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        $.post(form.attr('action'), form.serialize(), function(response) {
            const res = JSON.parse(response);
            alert(res.message);
            if (res.status === 'success') location.reload();
        });
    });
});
</script>
