<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<div>
  <h2>Product Items</h2>

  <!-- Add Item Button at the Top -->
  <div class="d-flex justify-content-between mb-3">
  <?php
include_once "../assets/config.php";

// Fetch all categories from the database
$category_sql = "SELECT * FROM product_categories";
$category_result = $conn->query($category_sql);
?>

<!-- Item Type Filter -->
<div>
  <label for="filter_item_type">Filter by Item Type: </label>
  <select id="filter_item_type" class="form-control" style="width: 200px; display: inline-block;" onchange="filterItems()">
    <option value="All">All</option>
    <?php
    // Dynamically generate option elements from the product_categories table
    if ($category_result->num_rows > 0) {
        while ($category_row = $category_result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($category_row['category_name']) . '">' . htmlspecialchars($category_row['category_name']) . '</option>';
        }
    }
    ?>
  </select>
</div>


    <!-- Add Item Button -->
    <button type="button" class="btn btn-secondary" style="height:40px" data-toggle="modal" data-target="#myModal">
      Add Item
    </button>
  </div>

  <!-- Product List Container -->
  <div class="product-list">
    <table class="table">
      <thead>
        <tr>
          <th class="text-center">S.N.</th>
          <th class="text-center">Image</th>
          <th class="text-center">Item Name</th>
          <th class="text-center">Item Type</th>
          <th class="text-center">Stock</th>
          <th class="text-center">Unit Price</th>
          <th class="text-center">Details</th>
          <th class="text-center" colspan="2">Action</th>
        </tr>
      </thead>
      <tbody id="product_table_body">
  <?php
  include_once "../assets/config.php";

  // Fetch all product items with their category names
  $sql = "SELECT product_items.*, product_categories.category_name 
          FROM product_items 
          INNER JOIN product_categories ON product_items.category_id = product_categories.category_id";
  $result = $conn->query($sql);
  $count = 1;

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
  ?>
  <tr class="product-row" data-item-type="<?= htmlspecialchars($row["category_name"]) ?>">
    <td class="text-center"><?= $count ?></td>
    <td class="text-center">
      <?php if ($row["product_image"]): ?>
        <img src="<?= htmlspecialchars($row["product_image"]) ?>" alt="<?= htmlspecialchars($row["product_name"]) ?>" style="width: 50px; height: 50px;">
      <?php else: ?>
        No Image
      <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($row["product_name"]) ?></td>
    <td><?= htmlspecialchars($row["category_name"]) ?></td>
    <td><?= htmlspecialchars($row["quantity"]) ?></td>
    <td><?= htmlspecialchars($row["price"]) ?></td>
    <td><?= htmlspecialchars($row["special_instructions"]) ?></td>
    <td class="text-center">
      <button class="btn btn-primary" style="height:40px" onclick="itemEditForm('<?= $row['product_id'] ?>')">Edit</button>
    </td>
    <td class="text-center">
      <button class="btn btn-danger" style="height:40px" onclick="itemDelete('<?= $row['product_id'] ?>')">Delete</button>
    </td>
  </tr>
  <?php
      $count++;
    }
  } else {
    echo "<tr><td colspan='9' class='text-center'>No items found</td></tr>";
  }
  ?>
</tbody>

    </table>
  </div>

  <!-- Modal for Adding Product -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">New Product Item</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <form id="productForm" enctype="multipart/form-data" onsubmit="addItems(); return false;">
            <div class="form-group">
              <label for="item_name">Item Name:</label>
              <input type="text" class="form-control" id="item_name" name="item_name" required>
            </div>
            <?php
include_once "../assets/config.php";

// Fetch all categories from the database
$category_sql = "SELECT * FROM product_categories";
$category_result = $conn->query($category_sql);
?>

<div class="form-group">
  <label for="item_type">Item Type:</label>
  <select id="item_type" name="item_type" class="form-control" required>
    <?php
    // Dynamically generate option elements from the product_categories table
    if ($category_result->num_rows > 0) {
        while ($category_row = $category_result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($category_row['category_name']) . '">' . htmlspecialchars($category_row['category_name']) . '</option>';
        }
    }
    ?>
  </select>
</div>

            <div class="form-group">
              <label for="stock">Stock:</label>
              <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
            <div class="form-group">
              <label for="price">Unit Price:</label>
              <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group">
              <label for="special_instructions">Special Instructions:</label>
              <textarea class="form-control" id="special_instructions" name="special_instructions"></textarea>
            </div>
            <div class="form-group">
              <label for="item_image">Item Image:</label>
              <input type="file" class="form-control-file" id="item_image" name="item_image" accept="image/*" required>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-secondary" style="height:40px">Add Item</button>
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


