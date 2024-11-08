<?php
session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div>
  <h2 class="text-center">Category List</h2>

  <!-- Center Filter and Add Category Button -->
  <div class="d-flex flex-wrap justify-content-center mb-3">
    <!-- Filter Categories -->
    <div class="mb-2">
      <label for="filter_category_type" class="mr-2">Filter by Category:</label>
      <select id="filter_category_type" class="form-control d-inline-block" style="width: 150px;" onchange="filterCategories()">
        <option value="All">All</option>
        <!-- Add specific category options if needed -->
      </select>
    </div>

    <!-- Add Category Button -->
    <div class="ml-2">
      <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#categoryModal">
        Add Category
      </button>
    </div>
  </div>

  <div class="category-list">
    <!-- Table view for larger screens -->
    <div class="table-responsive d-none d-md-block">
      <table class="table table-bordered">
          <thead class="thead">
              <tr>
                  <th class="text-center">S.N.</th>
                  <th class="text-center">Category Name</th>
                  <th class="text-center" colspan="2">Action</th>
              </tr>
          </thead>
          <tbody id="category_table_body">
              <?php
              include_once "../assets/config.php";

              $sql = "SELECT * FROM product_categories";
              $result = $conn->query($sql);
              $count = 1;

              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
              ?>
              <tr class="category-row">
                <td class="text-center"><?= $count ?></td>
                <td class="text-center"><?= htmlspecialchars($row["category_name"]) ?></td>
                <td class="text-center">
                  <button class="btn btn-primary btn-sm" onclick="categoryEditForm('<?= $row['category_id'] ?>')">Edit</button>
                </td>
                <td class="text-center">
                  <button class="btn btn-danger btn-sm" onclick="categoryDelete('<?= $row['category_id'] ?>')">Delete</button>
                </td>
              </tr>
              <?php
                  $count++;
                }
              } else {
                echo "<tr><td colspan='4' class='text-center'>No categories found</td></tr>";
              }
              ?>
          </tbody>
      </table>
    </div>

    <!-- Card view for smaller screens -->
    <div class="d-md-none">
      <?php
        if ($result->num_rows > 0) {
          mysqli_data_seek($result, 0); // Reset result pointer to re-use in card view
          $count = 1; // Reset count for card view
          while ($row = $result->fetch_assoc()) {
      ?>
        <div class="card mb-3">
          <div class="card-header">
            Category #<?= $count ?>
          </div>
          <div class="card-body">
            <p><strong>Category Name:</strong> <?= htmlspecialchars($row["category_name"]) ?></p>
            <div class="d-flex justify-content-between">
              <button class="btn btn-primary btn-sm" onclick="categoryEditForm('<?= $row['category_id'] ?>')">Edit</button>
              <button class="btn btn-danger btn-sm" onclick="categoryDelete('<?= $row['category_id'] ?>')">Delete</button>
            </div>
          </div>
        </div>
      <?php
            $count++;
          }
        } else {
          echo "<div class='text-center'>No categories found</div>";
        }
      ?>
    </div>
  </div>

  <!-- Modal for Adding Category -->
  <div class="modal fade" id="categoryModal" role="dialog">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title">New Category</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                  <form id="categoryForm" onsubmit="addCategory(); return false;">
                      <div class="form-group">
                          <label for="add_category_name">Category Name:</label>
                          <input type="text" class="form-control" id="add_category_name" name="category_name" required>
                      </div>
                      <div class="form-group">
                          <button type="submit" class="btn btn-secondary btn-block">Add Category</button>
                      </div>
                  </form>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default btn-block" data-dismiss="modal">Close</button>
              </div>
          </div>
      </div>
  </div>
</div>

<?php $conn->close(); ?>


