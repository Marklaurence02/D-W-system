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

<!-- Responsive Reservations Section -->
<div>
  <h3>Reservations</h3>
  <?php if (!empty($message)) { ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php } ?>

  <!-- Table view (visible on larger screens) -->
  <div class="table-responsive d-none d-md-block">
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
                <option value="Paid" <?= $row['status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
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

  <!-- Card view (visible on tablet and smaller screens) -->
  <div class="d-md-none">
    <?php
      if ($result && $result->num_rows > 0) {
        $count = 1; // Reset count for card view
        mysqli_data_seek($result, 0); // Reset result pointer to re-use in card view
        while ($row = $result->fetch_assoc()) {
    ?>
      <div class="card mb-3">
        <div class="card-header">
          <strong>Reservation #<?= $count ?></strong>
        </div>
        <div class="card-body">
          <p><strong>Reserved By:</strong> <?= htmlspecialchars($row["reserved_by"]) ?></p>
          <p><strong>Reservation Date:</strong> <?= htmlspecialchars($row["reservation_date"]) ?></p>
          <p><strong>Reservation Time:</strong> <?= htmlspecialchars($row["reservation_time"]) ?></p>
          <p><strong>Table Number:</strong> <?= htmlspecialchars($row["table_number"]) ?></p>
          <p><strong>Seating Capacity:</strong> <?= htmlspecialchars($row["seating_capacity"]) ?></p>
          <p><strong>Status:</strong>
            <span class="badge <?= $row["status"] == 'Pending' ? 'badge-warning' : ($row["status"] == 'Confirmed' ? 'badge-success' : ($row["status"] == 'Canceled' ? 'badge-danger' : 'badge-info')) ?>">
              <?= htmlspecialchars($row["status"]) ?>
            </span>
          </p>
          <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" class="reservation-form">
            <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($row['reservation_id']) ?>">
            <input type="hidden" name="ajax" value="1">
            <select name="status" class="form-control mb-2">
              <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
              <option value="Confirmed" <?= $row['status'] == 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
              <option value="Canceled" <?= $row['status'] == 'Canceled' ? 'selected' : '' ?>>Canceled</option>
              <option value="Rescheduled" <?= $row['status'] == 'Rescheduled' ? 'selected' : '' ?>>Rescheduled</option>
              <option value="Paid" <?= $row['status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
            </select>
            <button type="submit" class="btn btn-primary mt-2">Update</button>
          </form>
        </div>
      </div>
    <?php
          $count++;
        }
      } else {
        echo "<div class='text-center'>No reservations found.</div>";
      }
    ?>
  </div>
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
