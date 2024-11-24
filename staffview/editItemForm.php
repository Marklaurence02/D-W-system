<div class="container p-5">
    <h4>Edit Product Item Detail</h4>
    <?php
    include_once "../assets/config.php"; // Ensure correct path to config file

    $ID = $_POST['record']; // Get the product item ID from the POST request
    $qry = mysqli_query($conn, "SELECT * FROM product_items WHERE product_id='$ID'");
    $numberOfRow = mysqli_num_rows($qry);

    // Fetch all categories from the database
    $category_sql = "SELECT * FROM product_categories";
    $category_result = $conn->query($category_sql);

    if ($numberOfRow > 0) {
        $row1 = mysqli_fetch_array($qry); // Fetch only one row since ID is unique
        $product_image_path = $row1['product_image'];
    ?>
<form id="update-Items" onsubmit="updateItems(event, <?= $row1['product_id'] ?>); return false;" enctype="multipart/form-data">
    <input type="hidden" class="form-control" name="product_id" id="product_id" value="<?= $row1['product_id'] ?>">

    <!-- First Row for Product Name and Quantity -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input type="text" class="form-control" name="product_name" id="product_name" value="<?= $row1['product_name'] ?>" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control" name="quantity" id="quantity" value="<?= $row1['quantity'] ?>" required>
            </div>
        </div>
    </div>

    <!-- Second Row for Category and Price -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="category_id">Item Type:</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <?php
                    if ($category_result->num_rows > 0) {
                        while ($category_row = $category_result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($category_row['category_id']) . '"' . ($row1['category_id'] == $category_row['category_id'] ? ' selected' : '') . '>' . htmlspecialchars($category_row['category_name']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="price">Unit Price:</label>
                <input type="number" class="form-control" name="price" id="price" value="<?= $row1['price'] ?>" step="0.01" required>
            </div>
        </div>
    </div>

    <!-- Special Instructions Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="special_instructions">Special Instructions:</label>
                <textarea class="form-control" name="special_instructions" id="special_instructions"><?= $row1['special_instructions'] ?></textarea>
            </div>
        </div>
    </div>

    <!-- Image Upload Section -->
    <div class="form-group text-center">
        <label>Product Image:</label>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 text-center">
                <label for="product_image">Product Image:</label>
                <?php if (!empty($row1['product_image'])): ?>
                    <img src="<?= htmlspecialchars($row1['product_image']) ?>" alt="Product Image" class="img-thumbnail mb-2" style="max-width: 150px;">
                <?php endif; ?>
                <input type="file" class="form-control-file mt-2" name="item_image" id="item_image" accept="image/*">
                <small class="form-text text-muted">Leave empty if you don't want to change the image.</small>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="form-group text-center">
        <button type="submit" class="btn btn-primary">Update Item</button>
    </div>
</form>







    <?php
    } else {
        echo "<p>No item found with the provided ID.</p>";
    }
    ?>
</div>
