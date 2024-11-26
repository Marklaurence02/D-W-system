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
                <input type="number" class="form-control" name="price" id="price" value="<?= $row1['price'] ?>" step="1" required>
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
    <div class="form-group">
        <label for="item_image">Item Image:</label>
        <div class="image-upload-container">
            <!-- Current Database Image -->
            <div class="current-image-container mb-2" style="<?= empty($row1['product_image']) ? 'display: none;' : '' ?>">
                <h6>Current Image:</h6>
                <img src="<?= htmlspecialchars($row1['product_image']) ?>" alt="Current Image" style="max-width: 200px; height: auto;">
            </div>

            <!-- New Image Preview -->
            <div class="image-preview-container mb-2" style="display: none;">
            <h6>New Image:</h6>

                <img id="imagePreview" src="#" alt="Image Preview" style="max-width: 200px; height: auto;">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeNewImage()">Remove</button>
            </div>
            
            <input type="file" class="form-control-file" id="item_image" name="item_image" accept="image/*" onchange="previewImage(this)">
            <small class="form-text text-muted">Leave empty if you don't want to change the image.</small>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="form-group text-center">
        <button type="submit" class="btn btn-primary">Update Item</button>
    </div>
</form>

<script>

</script>

<style>
.image-upload-container {
    border: 2px dashed #ddd;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    background: #f8f9fa;
}

.image-preview-container, .current-image-container {
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
}

.image-preview-container img, .current-image-container img {
    max-width: 100%;
    height: auto;
    margin-bottom: 10px;
}

.remove-image {
    margin-top: 10px;
}

.form-control-file {
    display: block;
    margin: 0 auto;
}
</style>

    <?php
    } else {
        echo "<p>No item found with the provided ID.</p>";
    }
    ?>
</div>
