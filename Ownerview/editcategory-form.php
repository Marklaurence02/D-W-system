<?php
include_once "../assets/config.php";  // Include the database connection

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}




// Enable error reporting for debugging (you can remove this in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the category ID is provided in the POST request
if (isset($_POST['record'])) {
    $categoryID = intval($_POST['record']);  // Sanitize the input

    // Prepare the SQL query to get category details
    $stmt = $conn->prepare("SELECT * FROM product_categories WHERE category_id = ?");
    $stmt->bind_param("i", $categoryID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the category is found
    if ($result->num_rows > 0) {
        $categoryData = $result->fetch_assoc();
        ?>
        <div class="container p-5">
            <h4>Edit Category Details</h4>
            <form id="update-Category" onsubmit="updateCategory(event)" enctype="multipart/form-data">
                <input type="hidden" class="form-control" name="category_id" id="category_id" value="<?= htmlspecialchars($categoryData['category_id']) ?>">

                <div class="form-group">
                    <label for="category_name">Category Name:</label>
                    <input type="text" class="form-control" name="category_name" id="category_name" value="<?= htmlspecialchars($categoryData['category_name']) ?>" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>

        <?php
    } else {
        echo "<p>No category found with the given ID.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Invalid request. No category ID provided.</p>";
}
?>
