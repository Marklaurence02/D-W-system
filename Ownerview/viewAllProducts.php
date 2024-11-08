<?php
session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div>
  <h2 class="text-center">Product Items</h2>

  <!-- Filter and Add Item Button -->
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <?php
    include_once "../assets/config.php";

    // Fetch all categories from the database
    $category_sql = "SELECT * FROM product_categories";
    $category_result = $conn->query($category_sql);
    ?>

    <!-- Item Type Filter -->
    <div class="mb-2">
      <label for="filter_item_type" class="mr-2">Filter by Item Type:</label>
      <select id="filter_item_type" class="form-control d-inline-block" style="width: 200px;" onchange="filterItems()">
        <option value="All">All</option>
        <?php
        // Generate options from product_categories table
        if ($category_result->num_rows > 0) {
            while ($category_row = $category_result->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($category_row['category_name']) . '">' . htmlspecialchars($category_row['category_name']) . '</option>';
            }
        }
        ?>
      </select>
    </div>

    <!-- Add Item Button -->
    <div class="col-12 col-md-auto d-flex justify-content-center justify-content-md-end mt-2 mt-md-0">
      <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#myModal">
        Add Item
      </button>
    </div>
  </div>

  <!-- Product List Table (Visible on Desktop) -->
  <div class="product-list d-none d-md-block">
    <table class="table table-bordered">
      <thead class="thead">
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
        // Fetch product items and category names
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
          <td>&#8369;<?= htmlspecialchars($row["price"]) ?></td>
          <td><?= htmlspecialchars($row["special_instructions"]) ?></td>
          <td class="text-center">
            <button class="btn btn-primary btn-sm" onclick="itemEditForm('<?= $row['product_id'] ?>')">Edit</button>
          </td>
          <td class="text-center">
            <button class="btn btn-danger btn-sm" onclick="itemDelete('<?= $row['product_id'] ?>')">Delete</button>
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

  <!-- Product List Card View (Visible on Mobile) -->
  <div class="d-md-none">
    <?php
    if ($result->num_rows > 0) {
      mysqli_data_seek($result, 0); // Reset result pointer for reuse in card view
      $count = 1;
      while ($row = $result->fetch_assoc()) {
    ?>
      <div class="card mb-3">
        <div class="card-header">
          <strong>Product #<?= $count ?></strong>
        </div>
        <div class="card-body">
          <p><strong>Item Name:</strong> <?= htmlspecialchars($row["product_name"]) ?></p>
          <p><strong>Item Type:</strong> <?= htmlspecialchars($row["category_name"]) ?></p>
          <p><strong>Stock:</strong> <?= htmlspecialchars($row["quantity"]) ?></p>
          <p><strong>Unit Price:</strong> &#8369;<?= htmlspecialchars($row["price"]) ?></p>
          <p><strong>Details:</strong> <?= htmlspecialchars($row["special_instructions"]) ?></p>
          <p><strong>Image:</strong><br>
            <?php if ($row["product_image"]): ?>
              <img src="<?= htmlspecialchars($row["product_image"]) ?>" alt="<?= htmlspecialchars($row["product_name"]) ?>" style="width: 100px; height: 100px;">
            <?php else: ?>
              No Image
            <?php endif; ?>
          </p>
          <div class="d-flex justify-content-between">
            <button class="btn btn-primary btn-sm" onclick="itemEditForm('<?= $row['product_id'] ?>')">Edit</button>
            <button class="btn btn-danger btn-sm" onclick="itemDelete('<?= $row['product_id'] ?>')">Delete</button>
          </div>
        </div>
      </div>
    <?php
          $count++;
        }
      } else {
        echo "<div class='text-center'>No items found</div>";
      }
    ?>
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
          <div class="row">
            <div class="form-group col-12 col-md-6">
              <label for="item_name">Item Name:</label>
              <input type="text" class="form-control" id="item_name" name="item_name" required>
            </div>
            <div class="form-group col-12 col-md-6">
              <label for="item_type">Item Type:</label>
              <select id="item_type" name="item_type" class="form-control" required>
                <?php
                // Dynamically generate options for item types
                mysqli_data_seek($category_result, 0);
                if ($category_result->num_rows > 0) {
                    while ($category_row = $category_result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($category_row['category_name']) . '">' . htmlspecialchars($category_row['category_name']) . '</option>';
                    }
                }
                ?>
              </select>
            </div>
            <div class="form-group col-12 col-md-6">
              <label for="stock">Stock:</label>
              <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
            <div class="form-group col-12 col-md-6">
              <label for="price">Unit Price:</label>
              <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group col-12">
              <label for="special_instructions">Special Instructions:</label>
              <textarea class="form-control" id="special_instructions" name="special_instructions"></textarea>
            </div>
            <div class="form-group col-12">
              <label for="item_image">Item Image:</label>
              <input type="file" class="form-control-file" id="item_image" name="item_image" accept="image/*" required>
            </div>
            <div class="form-group col-12">
              <button type="submit" class="btn btn-secondary">Add Item</button>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>

</div>

<?php $conn->close(); ?>
