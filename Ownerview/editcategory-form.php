<?php
include_once "../assets/config.php";

session_name("owner_session");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['record'])) {
    $categoryID = intval($_POST['record']);

    $stmt = $conn->prepare("SELECT * FROM product_categories WHERE category_id = ?");
    $stmt->bind_param("i", $categoryID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $categoryData = $result->fetch_assoc();
        ?>
        <form id="editCategoryForm">
            <input type="hidden" name="category_id" value="<?= htmlspecialchars($categoryData['category_id']) ?>">
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" class="form-control" name="category_name" value="<?= htmlspecialchars($categoryData['category_name']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Category</button>
        </form>

        <script>
            $('#editCategoryForm').on('submit', function (event) {
                event.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: '../Ownerview/updatecatController.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert(response.message || 'Category updated successfully!');
                            $('#editModal').modal('hide');
                            location.reload();
                        } else {
                            alert(response.message || 'Failed to update category.');
                        }
                    },
                    error: function () {
                        alert('An unexpected error occurred.');
                    }
                });
            });
        </script>
        <?php
    } else {
        echo "<p>No category found with the given ID.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Invalid request. No category ID provided.</p>";
}
?>
