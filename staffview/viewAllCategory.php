<?php

include_once "../assets/config.php"; // Include database connection
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="text-center mb-3">
                <h2>Category Management</h2>
            </div>

            <!-- Filter and Add Category Button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div></div> <!-- Empty div for spacing -->

            </div>
            <!-- Table for Categories -->
            <div class="table-responsive">
                <table id="categoriesTable" class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM product_categories";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row["category_id"]) ?></td>
                                <td><?= htmlspecialchars($row["category_name"]) ?></td>
                            </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='3'>No categories found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal for Adding Category -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">New Category</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn btn-default btn-secondary" data-bs-dismiss="modal" style="height:40px">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Edit Category</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editCategoryContent">
                <!-- Form content will be dynamically loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        // Handle Edit Button Click
        $('.openPopup').on('click', function () {
            const categoryId = $(this).data('id');
            $('#editCategoryContent').html('<span>Loading...</span>');

            $.ajax({
                url: '../Ownerview/editcategory-form.php',
                type: 'POST',
                data: { record: categoryId },
                success: function (data) {
                    $('#editCategoryContent').html(data);
                    $('#editModal').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error("Error loading category form:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error loading category data. Please try again.',
                    });
                }
            });
        });

        // Handle modal events properly
        $('#editModal')
            .on('show.bs.modal', function () {
                $('body').addClass('modal-open');
            })
            .on('hidden.bs.modal', function () {
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                // Restore scrolling
                $('body').css('overflow', 'auto');
                $('html').css('overflow', 'auto');
            });

        // Function to refresh the DataTable
        function refreshCategoryTable() {
            location.reload(); // Force a full page reload to ensure clean state
        }

        // Listen for custom event after successful edit
        $(document).on('categoryEdited', function() {
            $('#editModal').modal('hide');
            setTimeout(function() {
                refreshCategoryTable();
            }, 300); // Small delay to ensure modal is fully closed
        });

        // Additional cleanup on modal show
        $('#editModal').on('show.bs.modal', function () {
            // Ensure no duplicate backdrops
            $('.modal-backdrop').remove();
        });

        // DataTable Initialization
        if ($('#categoriesTable').length) {
            new DataTable('#categoriesTable', {
                dom: '<"row"<"col-sm-12 col-md-12"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                paging: true,
                searching: true,
                ordering: true,
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search categories...",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    zeroRecords: "No matching records found",
                    paginate: {
                        first: '<i class="fa fa-angle-double-left"></i>',
                        previous: '<i class="fa fa-angle-left"></i>',
                        next: '<i class="fa fa-angle-right"></i>',
                        last: '<i class="fa fa-angle-double-right"></i>'
                    }
                },
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                }
            });
        }

        function updateCategory() {
            const categoryId = $('#edit_category_id').val();
            const categoryName = $('#edit_category_name').val();

            $.ajax({
                url: '../ajax/update_category.php',
                type: 'POST',
                data: {
                    category_id: categoryId,
                    category_name: categoryName
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Category updated successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            $('#editModal').modal('hide');
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open').css('overflow', 'auto');
                            $('html').css('overflow', 'auto');
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error updating category'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error updating category'
                    });
                }
            });
        }
    });

    // Update the categoryDelete function
    function categoryDelete(categoryId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/Ocontrols/deletecatController.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { category_id: categoryId },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                refreshCategoryList();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting the category'
                        });
                    }
                });
            }
        });
    }
</script>












