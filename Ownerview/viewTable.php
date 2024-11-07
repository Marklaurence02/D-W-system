<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<div class="table-section">
  <h2>Table Area</h2>

  <!-- Add Table Button -->
  <div class="d-flex justify-content-between mb-3">
    <div>
      <label for="filter_area">Filter by Area: </label>
      <select id="filter_area" class="form-control" style="width: 200px; display: inline-block;" onchange="filterTables()">
        <option value="All">All</option>
        <option value="Indoor">Indoor</option>
        <option value="Outdoor">Outdoor</option>
      </select>
    </div>

    <!-- Add Table Button -->
    <button type="button" class="btn btn-secondary" style="height:40px" data-toggle="modal" data-target="#addTableModal">
      Add Table
    </button>
  </div>

  <!-- Table List Container -->
  <div class="table-list">
    <table class="table">
      <thead>
        <tr>
          <th class="text-center">S.N.</th>
          <th class="text-center">Table Number</th>
          <th class="text-center">Seating Capacity</th>
          <th class="text-center">Area</th>
          <th class="text-center">Views</th>
          <th class="text-center" colspan="2">Action</th>
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
    <button class="btn btn-primary" style="height:40px" onclick="tableEditForm('<?= $row['table_id'] ?>')">Edit</button>
</td>



          <td class="text-center">
            <button class="btn btn-danger" style="height:40px" onclick="deleteTable('<?= $row['table_id'] ?>')">Delete</button>
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

  <div class="modal fade" id="addTableModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">New Table</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="tableForm" enctype="multipart/form-data" onsubmit="addTable(); return false;">
                    <div class="form-group">
                        <label for="table_number">Table Number:</label>
                        <input type="number" class="form-control" id="table_number" name="table_number" required>
                    </div>
                    <div class="form-group">
                        <label for="seating_capacity">Seating Capacity:</label>
                        <input type="number" class="form-control" id="seating_capacity" name="seating_capacity" required>
                    </div>
                    <div class="form-group">
                        <label for="area">Area:</label>
                        <select id="area" name="area" class="form-control" required>
                            <option value="Indoor">Indoor</option>
                            <option value="Outdoor">Outdoor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="front_image">Front View (Optional):</label>
                        <input type="file" class="form-control-file" id="front_image" name="front_image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="back_image">Back View (Optional):</label>
                        <input type="file" class="form-control-file" id="back_image" name="back_image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="left_image">Left View (Optional):</label>
                        <input type="file" class="form-control-file" id="left_image" name="left_image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="right_image">Right View (Optional):</label>
                        <input type="file" class="form-control-file" id="right_image" name="right_image" accept="image/*">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-secondary" style="height:40px">Add Table</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="height:40px">Close</button>
            </div>
        </div>
    </div>
</div>


</div>
