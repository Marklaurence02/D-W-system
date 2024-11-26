<?php
    session_start();
?>
<div class="container-fluid p-2">
    <div class="col-12">
        <div class="text-center mb-2">
            <h2>Product Items</h2>
        </div>

        <!-- Filter and Add Item Button -->
        <div class="d-flex justify-content-end mb-2">
            <?php
            include_once "../assets/config.php";

            // Fetch all categories from the database
            $category_sql = "SELECT * FROM product_categories";
            $category_result = $conn->query($category_sql);
            ?>

            <!-- Item Type Filter -->
            <div class="filter-container">
                <button type="button" class="btn-view-details" data-toggle="modal" data-target="#myModal">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
        </div>

        <!-- Product List Table (Visible on Desktop) -->
        <div class="product-list ">
            <!-- Product Table -->
            <div class="table-responsive">
                <table id="productTable" class="table table-striped table-hover table-bordered display nowrap">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Image</th>
                            <th class="text-center">Item Name</th>
                            <th class="text-center">Item Type</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-center">Details</th>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Delete</th>
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
                            $counter = 1;
                            while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr class="product-row" data-item-type="<?= htmlspecialchars($row["category_name"]) ?>">
                            <td class="text-center"><?= $counter ?></td>
                            <td class="text-center">
                                <?php if ($row["product_image"]): ?>
                                    <img src="<?= htmlspecialchars($row["product_image"]) ?>" alt="<?= htmlspecialchars($row["product_name"]) ?>" style="width: 50px; height: 50px;">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row["product_name"]) ?></td>
                            <td><?= htmlspecialchars($row["category_name"]) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row["quantity"]) ?></td>
                            <td class="text-center">&#8369;<?= htmlspecialchars($row["price"]) ?></td>
                            <td><?= htmlspecialchars($row["special_instructions"]) ?></td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm" onclick="itemEditForm('<?= $row['product_id'] ?>')">Edit</button>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm" onclick="itemDelete('<?= $row['product_id'] ?>')">Delete</button>
                            </td>
                        </tr>
                        <?php
                                $counter++;
                            }
                        } else {
                            echo "<tr><td colspan='10' class='text-center'>No items found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
                    <!-- First Row for Product Name and Quantity -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="item_name">Product Name:</label>
                                <input type="text" class="form-control" id="item_name" name="item_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock">Quantity:</label>
                                <input type="number" class="form-control" id="stock" name="stock" required>
                            </div>
                        </div>
                    </div>

                    <!-- Second Row for Category and Price -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="item_type">Item Type:</label>
                                <select id="item_type" name="item_type" class="form-control form-control-sm no-padding" required>
                                    <?php
                                    mysqli_data_seek($category_result, 0);
                                    if ($category_result->num_rows > 0) {
                                        while ($category_row = $category_result->fetch_assoc()) {
                                            echo '<option value="' . htmlspecialchars($category_row['category_name']) . '">' . htmlspecialchars($category_row['category_name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Unit Price:</label>
                                <input type="number" class="form-control" id="price" name="price" step="1" required>
                            </div>
                        </div>
                    </div>

                    <!-- Special Instructions Section -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="special_instructions">Special Instructions:</label>
                                <textarea class="form-control" id="special_instructions" name="special_instructions"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Image Upload Section -->
                    <div class="form-group">
                        <label for="item_image">Item Image:</label>
                        <div class="image-upload-container">
                            <!-- Image Preview -->
                            <div class="image-preview-container mb-2" style="display: none;">
                                <h6>New Image:</h6>
                                <img id="imagePreview" src="#" alt="Image Preview" style="max-width: 200px; height: auto;">
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeNewImage()">Remove</button>
                            </div>
                            
                            <input type="file" class="form-control-file" id="item_image" name="item_image" accept="image/*" onchange="previewImage(this)">
                            <small class="form-text text-muted">Supported formats: JPG, JPEG, PNG, GIF</small>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Edit Product</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editProductContent">
                <!-- Form content will be dynamically loaded here -->
            </div>
        </div>
    </div>
</div>

<?php $conn->close(); ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#productTable').DataTable({
        responsive: true,
        autoWidth: false,
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search products..."
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success',
                titleAttr: 'Export to Excel',
                title: 'Product Items Report'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info',
                titleAttr: 'Export to CSV',
                title: 'Product Items Report'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger',
                titleAttr: 'Export to PDF',
                title: 'Product Items Report',
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
                title: 'Product Items Report',
                autoPrint: true
            }
        ]
    });

    // Category filter handler
    $('#filter_item_type').change(function() {
        var selectedType = $(this).val();
        if (selectedType === 'All') {
            table.column(2).search('').draw();
        } else {
            table.column(2).search(selectedType).draw();
        }
    });
});

function previewImage(input) {
    const previewContainer = document.querySelector('.image-preview-container');
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '#';
        previewContainer.style.display = 'none';
    }
}

function removeNewImage() {
    const input = document.getElementById('item_image');
    const previewContainer = document.querySelector('.image-preview-container');
    const preview = document.getElementById('imagePreview');
    
    input.value = ''; // Clear the file input
    preview.src = '#';
    previewContainer.style.display = 'none';
}
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

/* Focus state */
.dataTables_filter input:focus {
    outline: none;
    border-color: #FD6610;
    box-shadow: 0 0 0 3px rgba(253, 102, 16, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .dataTables_filter input {
        width: 200px !important;
    }
}

/* Length (Entries per page) Styling */
.dataTables_length {
    float: left;
    margin-bottom: 20px;
}

.dataTables_length select {
    padding: 8px 30px 8px 15px;
    border: 2px solid #eee;
    border-radius: 20px;
    font-size: 14px;
    appearance: none;
    background: url("data:image/svg+xml,<svg height='10px' width='10px' viewBox='0 0 16 16' fill='%23000000' xmlns='http://www.w3.org/2000/svg'><path d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/></svg>") no-repeat;
    background-position: calc(100% - 12px) center;
    background-color: white;
}

.dataTables_length select:focus {
    border-color: #FD6610;
    outline: none;
}

/* Pagination Styling */
.dataTables_paginate {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    text-align: center;
}

.paginate_button {
    display: inline-block;
    min-width: 40px;
    height: 40px;
    line-height: 40px;
    margin: 0 5px;
    text-align: center;
    border-radius: 50%;
    color: #666;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
}

.paginate_button:hover {
    background-color: #f8f9fa;
    color: #FD6610;
}

.paginate_button.current {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    border: none;
}

.paginate_button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Info Text Styling */
.dataTables_info {
    margin-top: 20px;
    color: #666;
    font-size: 14px;
}

/* Table Styling */
#ordersTable {
    width: 100%;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}

#ordersTable th {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    padding: 15px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 14px;
    border: none;
}

#ordersTable td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

#ordersTable tbody tr:hover {
    background-color: #f8f9fa;
}

/* Status Button Styling */
.btn-status {
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    min-width: 120px;
    text-align: center;
}

/* Table Container */
.table-responsive {
    overflow-x: hidden !important; /* Prevent horizontal scroll */
}

/* Table Layout */
#ordersTable {
    width: 100% !important;
    margin: 0 !important;
}

/* Column Widths */
#ordersTable th,
#ordersTable td {
    white-space: normal; /* Allow text wrapping */
}

/* Specific Column Widths */
#ordersTable th:nth-child(1), /* O.N. */
#ordersTable td:nth-child(1) {
    width: 5%;
    min-width: 50px;
}

#ordersTable th:nth-child(2), /* Customer */
#ordersTable td:nth-child(2) {
    width: 15%;
    min-width: 120px;
}

#ordersTable th:nth-child(3), /* Contact */
#ordersTable td:nth-child(3) {
    width: 12%;
    min-width: 100px;
}

#ordersTable th:nth-child(4), /* Order Date */
#ordersTable td:nth-child(4) {
    width: 15%;
    min-width: 120px;
}

#ordersTable th:nth-child(5), /* Total */
#ordersTable td:nth-child(5) {
    width: 10%;
    min-width: 80px;
}

#ordersTable th:nth-child(6), /* Order Status */
#ordersTable td:nth-child(6) {
    width: 12%;
    min-width: 100px;
}

#ordersTable th:nth-child(7), /* Payment Method */
#ordersTable td:nth-child(7) {
    width: 15%;
    min-width: 120px;
}

#ordersTable th:nth-child(8), /* More Details */
#ordersTable td:nth-child(8) {
    width: 10%;
    min-width: 100px;
}

/* Cell Content */
#ordersTable td {
    word-wrap: break-word;
    max-width: 0;
}

/* Status Button */
.btn-status {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

/* Override Bootstrap column width */
.col-lg-10 {
    flex: 0 0 100% !important;
    max-width: 100% !important;
}

.col-md-12 {
    flex: 0 0 100% !important;
    max-width: 100% !important;
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

/* Responsive */
@media (max-width: 768px) {
    .filter-container {
        margin: 15px 0;
    }
    
    .filter-btn {
        padding: 6px 16px;
        font-size: 13px;
    }
    
    .filter-btn .badge {
        padding: 2px 6px;
        font-size: 11px;
    }
}

/* Payment Method Badge */
.payment-badge {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 13px;
    font-weight: 500;
    background-color: #e8f0fe;
    color: #1a73e8;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.payment-badge i {
    font-size: 14px;
}

/* Payment Method Types */
.payment-badge.cash {
    background-color: #e6ffe6;
    color: #0d8a0d;
}

.payment-badge.gcash {
    background-color: #f0e6ff;
    color: #6200ea;
}

/* View Details Button */
.btn-view-details {
    padding: 6px 14px;
    border-radius: 15px;
    font-size: 13px;
    font-weight: 500;
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.btn-view-details i {
    font-size: 14px;
}

.btn-view-details:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(253, 102, 16, 0.2);
    color: white;
}

/* Responsive Table */
@media (max-width: 1200px) {
    .dataTables_filter input {
        width: 200px;
    }
}

@media (max-width: 992px) {
    .filter-container {
        margin: 15px 0;
    }
    
    .filter-btn {
        padding: 6px 12px;
        font-size: 13px;
    }
    
    .payment-badge {
        padding: 4px 10px;
        font-size: 12px;
    }
    
    .btn-view-details {
        padding: 4px 10px;
        font-size: 12px;
    }
}

@media (max-width: 768px) {
    .dataTables_wrapper {
        padding: 15px;
    }
    
    .dataTables_filter input {
        width: 150px;
    }
    
    .dt-buttons {
        margin-bottom: 10px;
        width: 100%;
    }
    
    .dt-buttons .dt-button {
        width: 100%;
        margin-bottom: 5px;
    }
}

@media (max-width: 576px) {
    .filter-btn {
        padding: 4px 10px;
        font-size: 12px;
    }
    
    .filter-btn .badge {
        padding: 2px 6px;
        font-size: 11px;
    }
}

/* Only Order Status styling */
.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-width: 120px;
    justify-content: center;
}

.status-badge i {
    font-size: 14px;
}

/* Status Colors */
.status-pending {
    background-color: #fff5f5;
    color: #dc3545;
    border: 1px solid #dc3545;
}

.status-progress {
    background-color: #fff8e1;
    color: #ffc107;
    border: 1px solid #ffc107;
}

.status-progress i {
    animation: spin 2s linear infinite;
}

.status-completed {
    background-color: #f0fff4;
    color: #28a745;
    border: 1px solid #28a745;
}

.status-canceled {
    background-color: #f8f9fa;
    color: #6c757d;
    border: 1px solid #6c757d;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .status-badge {
        padding: 6px 12px;
        font-size: 12px;
        min-width: 100px;
    }
    
    .status-badge i {
        font-size: 12px;
    }
}

/* Modal Styling */
.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.modal-header {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 15px 20px;
    border: none;
}

.modal-title {
    font-weight: 600;
    font-size: 1.2rem;
    margin: 0;
}

.btn-close {
    background-color: white;
    opacity: 0.8;
    transition: all 0.2s ease;
}

.btn-close:hover {
    opacity: 1;
    transform: rotate(90deg);
}

.modal-body {
    padding: 20px;
}

.modal-dialog {
    max-width: 800px;
}

/* Responsive Modal */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 0.5rem;
    }
}

/* Add these styles for the order details table */
#orderDetailsTable {
    margin-bottom: 0;
}

#orderDetailsTable td {
    padding: 12px 15px;
    vertical-align: middle;
}

#orderDetailsTable td:first-child {
    width: 30%;
    background-color: #f8f9fa;
    font-weight: 500;
}

/* Status Dropdown in Modal */
.dropdown-toggle {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.dropdown-toggle::after {
    margin-left: 8px;
}

.dropdown-menu {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: none;
    padding: 8px;
}

.dropdown-item {
    padding: 8px 16px;
    border-radius: 5px;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #fff5f0;
    color: #FD6610;
}

/* Print Button Styling */
.dt-buttons {
    margin-bottom: 20px;
}

.dt-button {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%) !important;
    color: white !important;
    border: none !important;
    border-radius: 20px !important;
    padding: 8px 16px !important;
    font-size: 14px !important;
    transition: all 0.3s ease !important;
}

.dt-button:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(253, 102, 16, 0.2) !important;
}

/* Modal Body Padding */
.order-view-modal {
    padding: 20px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    #orderDetailsTable td:first-child {
        width: 40%;
    }
    
    .dt-button {
        width: 100%;
        margin-bottom: 10px;
    }
}

/* Remove padding from select */
select.no-padding {
    padding: 0 !important;
    height: 30px !important;
}

.image-upload-container {
    border: 2px dashed #ddd;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    background: #f8f9fa;
}

.image-preview-container {
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
}

.image-preview-container img {
    max-width: 100%;
    height: auto;
    margin-bottom: 10px;
}

.form-control-file {
    display: block;
    margin: 0 auto;
}
</style>
