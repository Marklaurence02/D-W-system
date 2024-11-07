<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<?php
include_once "../assets/config.php"; // Include database connection

// Handle form submission to change reservation status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reservation_id']) && isset($_POST['status'])) {
  $reservationId = intval($_POST['reservation_id']); // Convert to integer
  $status = $_POST['status']; // Get the new status

  // Log incoming data for debugging
  error_log("Reservation ID: " . $reservationId);
  error_log("Status: " . $status);

  // Prepare the SQL statement to update the reservation status
  $updateSql = "UPDATE reservations SET status = ?, updated_at = NOW() WHERE reservation_id = ?";
  $stmt = $conn->prepare($updateSql);

  if (!$stmt) {
      // Log SQL preparation error
      error_log("SQL Prepare Error: " . $conn->error);
      echo json_encode(['status' => 'error', 'message' => "SQL error: " . $conn->error]);
      exit;
  }

  $stmt->bind_param("si", $status, $reservationId);

  if ($stmt->execute()) {
      $message = "Reservation status updated successfully."; // Success message

      // If it's an AJAX request, return JSON response
      if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
          echo json_encode(['status' => 'success', 'message' => $message]);
          exit;
      }
  } else {
      $message = "Error updating reservation status: " . htmlspecialchars($stmt->error); // Error message
      error_log("SQL Execute Error: " . $stmt->error); // Log the SQL execution error

      // If it's an AJAX request, return JSON error response
      if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
          echo json_encode(['status' => 'error', 'message' => $message]);
          exit;
      }
  }

  $stmt->close();

  // For non-AJAX requests, redirect back to the same page with a message
  header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($message));
  exit; // Prevent further script execution
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

// Get the message from the URL if it exists
if (isset($_GET['message'])) {
  $message = htmlspecialchars($_GET['message']); // Display message if available
}
?>

<!-- Reservations Table -->
<div>
  <h3>Reservations</h3>
  <?php if (!empty($message)) { ?>
    <div class="alert alert-info"><?= $message ?></div> <!-- Show message -->
  <?php } ?>
  <table class="table table-striped">
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
    <tbody>
      <?php
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
      ?>
      <tr>
        <td class="text-center"><?= $count ?></td>
        <td class="text-center"><?= htmlspecialchars($row["reserved_by"]) ?></td>
        <td class="text-center"><?= htmlspecialchars($row["reservation_date"]) ?></td>
        <td class="text-center"><?= htmlspecialchars($row["reservation_time"]) ?></td>
        <td class="text-center"><?= htmlspecialchars($row["table_number"]) ?></td>
        <td class="text-center"><?= htmlspecialchars($row["seating_capacity"]) ?></td>
        <td class="text-center">
          <span class="badge <?= $row["status"] == 'Pending' ? 'badge-warning' : ($row["status"] == 'Confirmed' ? 'badge-success' : ($row["status"] == 'Canceled' ? 'badge-danger' : 'badge-info')) ?>">
            <?= htmlspecialchars($row["status"]) ?>
          </span>
        </td>
        <td class="text-center">
          <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" class="reservation-form">
            <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($row['reservation_id']) ?>">
            <input type="hidden" name="ajax" value="1">
            <select name="status" class="form-control">
              <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
              <option value="Confirmed" <?= $row['status'] == 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
              <option value="Canceled" <?= $row['status'] == 'Canceled' ? 'selected' : '' ?>>Canceled</option>
              <option value="Rescheduled" <?= $row['status'] == 'Rescheduled' ? 'selected' : '' ?>>Rescheduled</option>
            </select>
            <button type="submit" class="btn btn-primary mt-2">Update</button>
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

