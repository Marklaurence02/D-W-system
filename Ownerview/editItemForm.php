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

    <div class="row form-group">
        <div class="col-md-4">
            <label for="product_name">Product Name:</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="product_name" id="product_name" value="<?= $row1['product_name'] ?>" required>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label for="category_id">Item Type:</label>
        </div>
        <div class="col-md-8">
            <select id="category_id" name="category_id" class="form-control" required>
                <?php
                // Dynamically generate option elements from the product_categories table
                if ($category_result->num_rows > 0) {
                    while ($category_row = $category_result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($category_row['category_id']) . '"' . ($row1['category_id'] == $category_row['category_id'] ? ' selected' : '') . '>' . htmlspecialchars($category_row['category_name']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label for="quantity">Quantity:</label>
        </div>
        <div class="col-md-8">
            <input type="number" class="form-control" name="quantity" id="quantity" value="<?= $row1['quantity'] ?>" required>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label for="price">Unit Price:</label>
        </div>
        <div class="col-md-8">
            <input type="number" class="form-control" name="price" id="price" value="<?= $row1['price'] ?>" step="0.01" required>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label for="special_instructions">Special Instructions:</label>
        </div>
        <div class="col-md-8">
            <textarea class="form-control" name="special_instructions" id="special_instructions"><?= $row1['special_instructions'] ?></textarea>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label for="product_image">Product Image:</label>
        </div>
        <div class="col-md-8">
            <!-- Display current image if it exists -->
            <?php if (!empty($row1['product_image'])): ?>
                <p>Current Image:</p>
                <img src="<?= htmlspecialchars($row1['product_image']) ?>" alt="Product Image" style="max-width: 150px; height: auto;" />
            <?php endif; ?>
            <!-- Allow new image upload -->
            <input type="file" class="form-control-file" id="item_image" name="item_image" accept="image/*">
            <small class="form-text text-muted">Leave empty if you don't want to change the image.</small>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Update Item</button>
        </div>
    </div>
</form>



    <?php
    } else {
        echo "<p>No item found with the provided ID.</p>";
    }
    ?>
</div>
