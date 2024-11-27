<?php
    session_start();

?>
<div class="container-fluid p-2">
    <div class="col-12">
        <div class="text-center mb-2">
            <h2>Table Area</h2>
        </div>

        <!-- Filter and Add Table Button -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <?php
            include_once "../assets/config.php";
            ?>

            <!-- Filter Container -->
            <div class="filter-container">
                <div class="status-filters">
                    <button class="filter-btn active" data-status="all">All Tables</button>
                    <button class="filter-btn" data-status="Indoor">Indoor</button>
                    <button class="filter-btn" data-status="Outdoor">Outdoor</button>
                </div>
            </div>

            <!-- Add Table Button -->
            <div class="filter-container">
                <button type="button" class="btn-view-details" data-toggle="modal" data-target="#addTableModal">
                    <i class="fas fa-plus"></i> Add Table
                </button>
            </div>
        </div>

        <!-- Table List -->
        <div class="product-list">
            <!-- Table -->
            <div class="table-responsive">
                <table id="tableData" class="table table-striped table-hover table-bordered display nowrap" style="width:100%">
                    <thead class="thead">
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Table Number</th>
                            <th class="text-center">Seating Capacity</th>
                            <th class="text-center">Area</th>
                            <th class="text-center">Views</th>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Delete</th>
                        </tr>
                    </thead>
                    <tbody id="table_body">
                        <?php
                        include_once "../assets/config.php";

                        // Fetch all tables and their images
                        $sql = "SELECT t.*, GROUP_CONCAT(i.image_path) AS images 
                                FROM tables t 
                                LEFT JOIN table_images i ON t.table_id = i.table_id 
                                GROUP BY t.table_id";
                        $result = $conn->query($sql);
                        $count = 1;

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td class="text-center"><?= $count ?></td>
                            <td class="text-center"><?= htmlspecialchars($row["table_number"]) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row["seating_capacity"]) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row["area"]) ?></td>
                            <td class="text-center">
                                <?php
                                if ($row["images"]) {
                                    $images = explode(',', $row["images"]);
                                    foreach ($images as $image) {
                                        echo "<img src='". htmlspecialchars($image) ."' alt='Table Image' style='width: 50px; height: 50px; margin-right: 5px;'>";
                                    }
                                } else {
                                    echo "No Images";
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-warning" onclick="tableEditForm('<?= $row['table_id'] ?>')">Edit</button>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm" onclick="deleteTable('<?= $row['table_id'] ?>')">Delete</button>
                            </td>
                        </tr>
                        <?php
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No tables found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Table Modal -->
<div class="modal fade" id="addTableModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">New Table</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="tableForm" enctype="multipart/form-data" onsubmit="addTable(); return false;">
                    <div class="row">
                        <!-- Table Number and Seating Capacity -->
                        <div class="form-group col-12 col-lg-6 mb-3">
                            <label for="table_number">Table Number:</label>
                            <input type="number" class="form-control" id="table_number" name="table_number" required>
                        </div>
                        <div class="form-group col-12 col-lg-6 mb-3">
                            <label for="seating_capacity">Seating Capacity:</label>
                            <input type="number" class="form-control" id="seating_capacity" name="seating_capacity" required>
                        </div>

                        <!-- Area (Full Width) -->
                        <div class="form-group col-12 mb-3">
                            <label for="area">Area:</label>
                            <select id="area" name="area" class="form-control" required>
                                <option value="Indoor">Indoor</option>
                                <option value="Outdoor">Outdoor</option>
                            </select>
                        </div>

                        <!-- File Uploads -->
                        <div class="form-group col-12 col-lg-6 mb-3">
                            <label for="front_image">Front View (Optional):</label>
                            <input type="file" class="form-control-file" id="front_image" name="front_image" accept="image/*">
                            <div class="image-preview-container mt-2">
                                <img id="frontPreview" src="/Images/noimage.jpeg" alt="Front Preview" class="img-thumbnail" style="max-width: 150px;">
                            </div>
                        </div>
                        <div class="form-group col-12 col-lg-6 mb-3">
                            <label for="back_image">Back View (Optional):</label>
                            <input type="file" class="form-control-file" id="back_image" name="back_image" accept="image/*">
                            <div class="image-preview-container mt-2">
                                <img id="backPreview" src="/Images/noimage.jpeg" alt="Back Preview" class="img-thumbnail" style="max-width: 150px;">
                            </div>
                        </div>
                        <div class="form-group col-12 col-lg-6 mb-3">
                            <label for="left_image">Left View (Optional):</label>
                            <input type="file" class="form-control-file" id="left_image" name="left_image" accept="image/*">
                            <div class="image-preview-container mt-2">
                                <img id="leftPreview" src="/Images/noimage.jpeg" alt="Left Preview" class="img-thumbnail" style="max-width: 150px;">
                            </div>
                        </div>
                        <div class="form-group col-12 col-lg-6 mb-3">
                            <label for="right_image">Right View (Optional):</label>
                            <input type="file" class="form-control-file" id="right_image" name="right_image" accept="image/*">
                            <div class="image-preview-container mt-2">
                                <img id="rightPreview" src="/Images/noimage.jpeg" alt="Right Preview" class="img-thumbnail" style="max-width: 150px;">
                            </div>
                        </div>

                        <!-- Submit Button (Full Width) -->
                        <div class="form-group col-12 text-center">
                            <button type="submit" class="btn btn-secondary" style="height:40px">Add Table</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<!-- Edit Table Modal -->
<div class="modal fade" id="editTableModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Table</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="editTableContent">
                    <!-- The edit form will be loaded here via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="height:40px">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#tableData').DataTable({
        responsive: true,
        autoWidth: false,
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search tables..."
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success',
                titleAttr: 'Export to Excel',
                title: 'Table List Report'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info',
                titleAttr: 'Export to CSV',
                title: 'Table List Report'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger',
                titleAttr: 'Export to PDF',
                title: 'Table List Report',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-primary',
                titleAttr: 'Print Table',
                title: 'Table List Report',
                autoPrint: true
            }
        ],
        columnDefs: [
            { orderable: false, targets: [4, 5, 6] }
        ]
    });

    // Filter button click handler
    $('.filter-btn').on('click', function() {
        var status = $(this).data('status');
        
        // Remove active class from all buttons and add to clicked button
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Apply the filter
        if (status === 'all') {
            table.search('').columns().search('').draw();
        } else {
            table.column(3).search(status).draw(); // 3 is the index of the Area column
        }
    });

    // Initial filter (if needed)
    $('.filter-btn.active').trigger('click');
});

// Add Table Form Submission
$('#addTableForm').on('submit', function(e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    
    $.ajax({
        url: '../controller/table_controller.php?action=add_table',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            var result = JSON.parse(response);
            if (result.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: result.message
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: result.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while processing your request'
            });
        }
    });
});

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Update your form inputs to include preview functionality
document.getElementById('front_image').onchange = function() {
    previewImage(this, 'frontPreview');
};
document.getElementById('back_image').onchange = function() {
    previewImage(this, 'backPreview');
};
document.getElementById('left_image').onchange = function() {
    previewImage(this, 'leftPreview');
};
document.getElementById('right_image').onchange = function() {
    previewImage(this, 'rightPreview');
};
</script>

<style>
/* DataTable Container */
.dataTables_wrapper {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

/* Search Box Styling */
.dataTables_filter {
    position: relative;
    margin-bottom: 20px;
}

.dataTables_filter label {
    display: flex;
    align-items: center;
    margin: 0;
    width: 100%;
    position: relative;
}

.dataTables_filter input {
    width: 300px !important;
    height: 40px;
    padding: 8px 16px 8px 40px !important;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    font-size: 14px;
    transition: all 0.3s ease;
    margin: 0 !important;
}

/* Hide the "Search:" text */
.dataTables_filter label > span,
.dataTables_filter label::before {
    display: none;
}

/* Add search icon */
.dataTables_filter::before {
    content: "\f002";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    z-index: 1;
    pointer-events: none;
}

/* Filter Styling */
.filter-container {
    margin: 20px 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding: 4px;
}

.status-filters {
    display: flex;
    gap: 10px;
    flex-wrap: nowrap;
    min-width: max-content;
}

.filter-btn {
    padding: 8px 20px;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    background: white;
    color: #666;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.filter-btn:hover {
    background-color: #f8f9fa;
    border-color: #FD6610;
    color: #FD6610;
}

.filter-btn.active {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    border: none;
    box-shadow: 0 2px 4px rgba(253, 102, 16, 0.2);
}

.filter-btn .badge {
    background: rgba(255, 255, 255, 0.2);
    color: inherit;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    margin-left: 4px;
}

/* Modal Styling */
.modal-content {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.modal-header {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    border-radius: 8px 8px 0 0;
}

.modal-header .close {
    color: white;
    opacity: 1;
}

.modal-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 1rem;
}

.form-control {
    border-radius: 4px;
    border: 1px solid #e0e0e0;
    padding: 8px 12px;
}

.form-control:focus {
    border-color: #FD6610;
    box-shadow: 0 0 0 0.2rem rgba(253, 102, 16, 0.25);
}

.image-preview-container {
    position: relative;
    display: inline-block;
}

</style>
