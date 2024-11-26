<?php
include_once "../assets/config.php"; // Ensure correct path to your config file

// Check if the table ID is provided in the POST request
if (isset($_POST['record'])) {
    $tableID = intval($_POST['record']);

    // Fetch the table details
    $qry = mysqli_query($conn, "SELECT * FROM tables WHERE table_id='$tableID'");
    if (!$qry) {
        echo "<p>Error loading table details.</p>";
        error_log("Error fetching table details: " . mysqli_error($conn));
        exit();
    }

    // Fetch the table data
    $tableData = mysqli_fetch_array($qry);

    // Check if data was fetched correctly
    if (!$tableData) {
        echo "<p>No table found with the specified ID.</p>";
        exit();
    }

    // Access the array keys with a fallback to avoid undefined warnings
    $table_id = isset($tableData['table_id']) ? $tableData['table_id'] : null;
    $table_number = isset($tableData['table_number']) ? $tableData['table_number'] : null;
    $seating_capacity = isset($tableData['seating_capacity']) ? $tableData['seating_capacity'] : null;
    $area = isset($tableData['area']) ? $tableData['area'] : null;
    $is_available = isset($tableData['is_available']) ? $tableData['is_available'] : 1; // Default to available

    // Fetch images related to the table
    $imageQry = mysqli_query($conn, "SELECT * FROM table_images WHERE table_id='$tableID'");
    $tableImages = [];
    while ($imageRow = mysqli_fetch_assoc($imageQry)) {
        $tableImages[$imageRow['position']] = $imageRow['image_path'];
    }

    ?>
<form id="update-Table" onsubmit="updateTables(event)" enctype="multipart/form-data">
    <!-- Hidden input to store the table_id -->
    <input type="hidden" name="table_id" id="table_id" value="<?= $table_id ?>">

    <div class="row">
        <!-- First Row for Table Number and Seating Capacity -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="table_number">Table Number:</label>
                <input type="number" class="form-control" name="table_number" id="table_number" value="<?= $table_number ?>" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="seating_capacity">Seating Capacity:</label>
                <input type="number" class="form-control" name="seating_capacity" id="seating_capacity" value="<?= $seating_capacity ?>" required>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Second Row for Area and Availability -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="area">Area:</label>
                <select class="form-control" name="area" id="area" required>
                    <option value="Indoor" <?= $area == 'Indoor' ? 'selected' : '' ?>>Indoor</option>
                    <option value="Outdoor" <?= $area == 'Outdoor' ? 'selected' : '' ?>>Outdoor</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="is_available">Availability:</label>
                <select class="form-control" name="is_available" id="is_available" required>
                    <option value="1" <?= $is_available == 1 ? 'selected' : '' ?>>Available</option>
                    <option value="0" <?= $is_available == 0 ? 'selected' : '' ?>>Not Available</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Image Upload Section -->
    <div class="form-group">
        <label>Table Images:</label>
        <div class="row">
            <!-- First Row: Back View and Left View -->
            <div class="col-md-6 text-center">
                <label for="back_view">Back View:</label>
                <div class="image-preview-container">
                    <?php if (isset($tableImages['back view']) && $tableImages['back view']): ?>
                        <img src="<?= $tableImages['back view'] ?>" alt="Back View" class="img-thumbnail mb-2" style="max-width: 150px;" id="back_view_preview">
                    <?php else: ?>
                        <img src="/Images/noimage.jpeg" alt="Back View" class="img-thumbnail mb-2" style="max-width: 150px;" id="back_view_preview">
                    <?php endif; ?>
                </div>
                <input type="file" name="back_view" id="back_view" class="form-control-file mt-2" onchange="previewImage(this, 'back_view_preview')">
            </div>

            <div class="col-md-6 text-center">
                <label for="left_view">Left View:</label>
                <div class="image-preview-container">
                    <?php if (isset($tableImages['left view']) && $tableImages['left view']): ?>
                        <img src="<?= $tableImages['left view'] ?>" alt="Left View" class="img-thumbnail mb-2" style="max-width: 150px;" id="left_view_preview">
                    <?php else: ?>
                        <img src="/Images/noimage.jpeg" alt="Left View" class="img-thumbnail mb-2" style="max-width: 150px;" id="left_view_preview">
                    <?php endif; ?>
                </div>
                <input type="file" name="left_view" id="left_view" class="form-control-file mt-2" onchange="previewImage(this, 'left_view_preview')">
            </div>
        </div>

        <div class="row">
            <!-- Second Row: Right View and Front View -->
            <div class="col-md-6 text-center">
                <label for="right_view">Right View:</label>
                <div class="image-preview-container">
                    <?php if (isset($tableImages['right view']) && $tableImages['right view']): ?>
                        <img src="<?= $tableImages['right view'] ?>" alt="Right View" class="img-thumbnail mb-2" style="max-width: 150px;" id="right_view_preview">
                    <?php else: ?>
                        <img src="/Images/noimage.jpeg" alt="Right View" class="img-thumbnail mb-2" style="max-width: 150px;" id="right_view_preview">
                    <?php endif; ?>
                </div>
                <input type="file" name="right_view" id="right_view" class="form-control-file mt-2" onchange="previewImage(this, 'right_view_preview')">
            </div>

            <div class="col-md-6 text-center">
                <label for="front_view">Front View:</label>
                <div class="image-preview-container">
                    <?php if (isset($tableImages['front view']) && $tableImages['front view']): ?>
                        <img src="<?= $tableImages['front view'] ?>" alt="Front View" class="img-thumbnail mb-2" style="max-width: 150px;" id="front_view_preview">
                    <?php else: ?>
                        <img src="/Images/noimage.jpeg" alt="Front View" class="img-thumbnail mb-2" style="max-width: 150px;" id="front_view_preview">
                    <?php endif; ?>
                </div>
                <input type="file" name="front_view" id="front_view" class="form-control-file mt-2" onchange="previewImage(this, 'front_view_preview')">
            </div>
        </div>
    </div>

    <!-- Add this style section -->
    <style>
    .image-preview-container {
        position: relative;
        display: inline-block;
    }

    .btn-remove-image {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #ff4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        line-height: 20px;
        text-align: center;
        cursor: pointer;
        padding: 0;
        font-size: 14px;
    }

    .btn-remove-image:hover {
        background: #cc0000;
    }
    </style>

    <!-- Submit Button -->
    <div class="form-group text-center">
        <button type="submit" class="btn btn-primary">Update Table</button>
    </div>
</form>

<!-- Update the script section -->
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const container = preview.parentElement;
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            // Remove any existing remove button
            const existingBtn = container.querySelector('.btn-remove-image');
            if (existingBtn) {
                existingBtn.remove();
            }
            // Add new remove button
            const removeBtn = document.createElement('button');
            removeBtn.className = 'btn-remove-image';
            removeBtn.innerHTML = '&times;';
            removeBtn.onclick = function() {
                removeImage(previewId, input.id);
            };
            container.appendChild(removeBtn);
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage(previewId, inputId) {
    const preview = document.getElementById(previewId);
    const input = document.getElementById(inputId);
    const removeBtn = preview.parentElement.querySelector('.btn-remove-image');
    
    preview.src = '/Images/noimage.jpeg';
    input.value = ''; // Clear the file input
    if (removeBtn) {
        removeBtn.remove(); // Remove the remove button
    }
}
</script>








    <?php
} else {
    echo "<p>No table ID provided.</p>";
    error_log("No table ID provided in the POST request.");
}
?>
