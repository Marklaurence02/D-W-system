<?php
session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="table-section">
  <h2>Table Area</h2>

<!-- Add Table Button and Filter -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
  <!-- Filter Dropdown -->
  <div class="mb-2">
    <label for="filter_area">Filter by Area: </label>
    <select id="filter_area" class="form-control d-inline-block" style="width: 200px;" onchange="filterTables()">
      <option value="All">All</option>
      <option value="Indoor">Indoor</option>
      <option value="Outdoor">Outdoor</option>
    </select>
  </div>

  <!-- Add Table Button, centered on smaller screens, aligned right on large screens -->
  <div class="d-flex col-12 col-md-auto justify-content-center justify-content-lg-end mt-2 mt-md-0">
    <button type="button" class="btn btn-secondary" style="height:40px" data-toggle="modal" data-target="#addTableModal">
      Add Table
    </button>
  </div>
</div>

  <!-- Table List (Visible on Desktop) -->
  <div class="table-responsive">
  <table id="tableData" class="table table-bordered">
  <thead class="thead-dark">
    <tr>
      <th class="text-center">S.N.</th>
      <th class="text-center">Table Number</th>
      <th class="text-center">Seating Capacity</th>
      <th class="text-center">Area</th>
      <th class="text-center">Views</th>
      <th class="text-center">Edit</th>
      <th class="text-center">Delete</th>
    </tr>
  </thead>
  <tbody id="table_body">
    <?php
    include_once "../assets/config.php";

    // Fetch all tables and their images
    $sql = "SELECT t.*, GROUP_CONCAT(i.image_path) AS images 
            FROM tables t 
            LEFT JOIN table_images i ON t.table_id = i.table_id 
            GROUP BY t.table_id";
    $result = $conn->query($sql);
    $count = 1;

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
    ?>
    <tr>
      <td class="text-center"><?= $count ?></td>
      <td class="text-center"><?= htmlspecialchars($row["table_number"]) ?></td>
      <td class="text-center"><?= htmlspecialchars($row["seating_capacity"]) ?></td>
      <td class="text-center"><?= htmlspecialchars($row["area"]) ?></td>
      <td class="text-center">
        <?php
          if ($row["images"]) {
            $images = explode(',', $row["images"]);
            foreach ($images as $image) {
              echo "<img src='". htmlspecialchars($image) ."' alt='Table Image' style='width: 50px; height: 50px; margin-right: 5px;'>";
            }
          } else {
            echo "No Images";
          }
        ?>
      </td>
      <td class="text-center">
      <button class="btn btn-warning" onclick="tableEditForm('<?= $row['table_id'] ?>')">Edit</button>
      </td>
      <td class="text-center">
        <button class="btn btn-danger btn-sm" onclick="deleteTable('<?= $row['table_id'] ?>')">Delete</button>
      </td>
    </tr>
    <?php
        $count++;
      }
    } else {
      echo "<tr><td colspan='7' class='text-center'>No tables found</td></tr>";
    }
    ?>
  </tbody>
</table>

  </div>

  <!-- Card View for Table List (Visible on Mobile) -->
  <div class="d-lg-none">
    <?php
    if ($result->num_rows > 0) {
      mysqli_data_seek($result, 0); // Reset result pointer to re-use in card view
      $count = 1;
      while ($row = $result->fetch_assoc()) {
    ?>
      <div class="card mb-3">
        <div class="card-header">
          <strong>Table #<?= htmlspecialchars($row["table_number"]) ?></strong>
        </div>
        <div class="card-body">
          <p><strong>Seating Capacity:</strong> <?= htmlspecialchars($row["seating_capacity"]) ?></p>
          <p><strong>Area:</strong> <?= htmlspecialchars($row["area"]) ?></p>
          <p><strong>Views:</strong><br>
            <?php
              if ($row["images"]) {
                $images = explode(',', $row["images"]);
                foreach ($images as $image) {
                  echo "<img src='". htmlspecialchars($image) ."' alt='Table Image' style='width: 100px; height: 100px; margin-right: 5px;'>";
                }
              } else {
                echo "No Images";
              }
            ?>
          </p>
          <div class="d-flex justify-content-between">
            <button class="btn btn-primary btn-sm" onclick="tableEditForm('<?= $row['table_id'] ?>')">Edit</button>
            <button class="btn btn-danger btn-sm" onclick="deleteTable('<?= $row['table_id'] ?>')">Delete</button>
          </div>
        </div>
      </div>
    <?php
          $count++;
        }
      } else {
        echo "<div class='text-center'>No tables found</div>";
      }
    ?>
  </div>

<!-- Modal for Adding Table -->
<div class="modal fade" id="addTableModal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">New Table</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form id="tableForm" enctype="multipart/form-data" onsubmit="addTable(); return false;">
          <div class="row">
            <!-- Table Number and Seating Capacity -->
            <div class="form-group col-12 col-lg-6 mb-3">
              <label for="table_number">Table Number:</label>
              <input type="number" class="form-control" id="table_number" name="table_number" required>
            </div>
            <div class="form-group col-12 col-lg-6 mb-3">
              <label for="seating_capacity">Seating Capacity:</label>
              <input type="number" class="form-control" id="seating_capacity" name="seating_capacity" required>
            </div>

            <!-- Area (Full Width) -->
            <div class="form-group col-12 mb-3">
              <label for="area">Area:</label>
              <select id="area" name="area" class="form-control" required>
                <option value="Indoor">Indoor</option>
                <option value="Outdoor">Outdoor</option>
              </select>
            </div>

            <!-- File Uploads -->
            <div class="form-group col-12 col-lg-6 mb-3">
              <label for="front_image">Front View (Optional):</label>
              <input type="file" class="form-control-file" id="front_image" name="front_image" accept="image/*">
            </div>
            <div class="form-group col-12 col-lg-6 mb-3">
              <label for="back_image">Back View (Optional):</label>
              <input type="file" class="form-control-file" id="back_image" name="back_image" accept="image/*">
            </div>
            <div class="form-group col-12 col-lg-6 mb-3">
              <label for="left_image">Left View (Optional):</label>
              <input type="file" class="form-control-file" id="left_image" name="left_image" accept="image/*">
            </div>
            <div class="form-group col-12 col-lg-6 mb-3">
              <label for="right_image">Right View (Optional):</label>
              <input type="file" class="form-control-file" id="right_image" name="right_image" accept="image/*">
            </div>

            <!-- Submit Button (Full Width) -->
            <div class="form-group col-12 text-center">
              <button type="submit" class="btn btn-secondary" style="height:40px">Add Table</button>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="height:40px">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Table Modal -->
<div class="modal fade" id="editTableModal" tabindex="-1" role="dialog" aria-labelledby="editTableModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editTableModalLabel">Edit Table</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editTableContent">
                <!-- The table form will be loaded here dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" onclick="updateTables(event)">Save changes</button>
            </div>
        </div>
    </div>
</div>




</div>



<script>
$(document).ready(function () {
    // Initialize DataTable
    var table = $('#tableData').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        columnDefs: [
            { orderable: false, targets: [4, 5, 6] } // Disable ordering for certain columns
        ]
    });

    // Add event listener to the "Filter by Area" dropdown
    $('#filter_area').on('change', function () {
        var selectedValue = $(this).val();

        if (selectedValue === 'All') {
            // Show all rows when "All" is selected
            table.column(3).search('').draw();
        } else {
            // Filter rows based on selected value
            table.column(3).search(selectedValue).draw();
        }
    });
});

</script>
