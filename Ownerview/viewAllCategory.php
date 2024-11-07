<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<div>
  <h2>Category List</h2>

  <!-- Add Category Button at the Top -->
  <div class="d-flex justify-content-between mb-3">
    <!-- Filter Categories (Optional) -->
    <div>
      <label for="filter_category_type">Filter by Category: </label>
      <select id="filter_category_type" class="form-control" style="width: 200px; display: inline-block;" onchange="filterCategories()">
        <option value="All">All</option>
        <!-- Add your specific category options if needed -->
      </select>
    </div>

    <!-- Add Category Button -->
    <button type="button" class="btn btn-secondary" style="height:40px" data-toggle="modal" data-target="#categoryModal">
      Add Category
    </button>
  </div>

  <div class="category-list">
    <table class="table">
        <thead>
            <tr>
                <th class="text-center">S.N.</th>
                <th class="text-center">Category Name</th>
                <th class="text-center" colspan="2">Action</th>
            </tr>
        </thead>
        <tbody id="category_table_body">
            <!-- Category rows will be dynamically updated here via refreshCategoryList() -->
            <?php
            include_once "../assets/config.php";

            // Fetch all categories
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
                <button class="btn btn-primary" style="height:40px" onclick="categoryEditForm('<?= $row['category_id'] ?>')">Edit</button>
              </td>
              <td class="text-center">
                <button class="btn btn-danger" style="height:40px" onclick="categoryDelete('<?= $row['category_id'] ?>')">Delete</button>
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
                        <button type="submit" class="btn btn-secondary" style="height:40px">Add Category</button>
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
