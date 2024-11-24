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
  <div class="product-list ">
    <!-- Product Table -->
<table id="productTable" class="table table-bordered">
  <thead class="thead">
    <tr>
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
      while ($row = $result->fetch_assoc()) {
    ?>
    <tr class="product-row" data-item-type="<?= htmlspecialchars($row["category_name"]) ?>">
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
          $count++;
      }
    } else {
      echo "<tr><td colspan='9' class='text-center'>No items found</td></tr>";
    }
    ?>
  </tbody>
</table>

<!-- Initialize DataTable -->

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
              <input type="number" class="form-control" id="price" name="price" step="1" required>
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


</div>

<?php $conn->close(); ?>


<script>
$(document).ready(function () {
    // Get the current date in the format YYYY-MM-DD
    var today = new Date();
    var formattedDate = today.getFullYear() + '-' + (today.getMonth() + 1).toString().padStart(2, '0') + '-' + today.getDate().toString().padStart(2, '0');

    var table = $('#productTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: function() {
                    return 'Product Items Report - ' + formattedDate;
                },
                text: 'Export to Excel',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                },
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var logo = '<image xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" x="0" y="0" width="1" height="1" r="1" c="1"><imageData r:id="rId1"/></image>';
                    sheet.insertBefore(logo, sheet.firstChild);
                    var sheetHeader = sheet.querySelector('sheetData');
                    sheetHeader.insertBefore('<row><c r="A1" t="inlineStr"><is><t style="font-size:14px; font-weight:bold; color:#FF6A13;">Product Items Report - ' + formattedDate + '</t></is></c></row>', sheetHeader.firstChild);
                }
            },
            {
                extend: 'csvHtml5',
                title: function() {
                    return 'Product Items Report - ' + formattedDate;
                },
                text: 'Export to CSV',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdfHtml5',
                title: function() {
                    return 'Product Items Report - ' + formattedDate;
                },
                text: 'Export to PDF',
                orientation: 'portrait',
                pageSize: 'A4',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                },
                customize: function(doc) {
                    doc.content.splice(0, 0, {
                        image: '/Images/logo.png',
                        width: 100,
                        height: 100
                    });
                    doc.content.splice(1, 0, {
                        text: 'Product Items Report - ' + formattedDate,
                        alignment: 'center',
                        fontSize: 16,
                        margin: [0, 10],
                        color: '#FF6A13'
                    });
                    doc.styles.tableHeader = {
                        fillColor: '#FF6A13',
                        color: '#fff',
                        fontSize: 12,
                        bold: true
                    };
                    doc.styles.tableBodyEven = { fillColor: '#FFEBE0' };
                    doc.styles.tableBodyOdd = { fillColor: '#FFE0CC' };
                }
            },
            {
                extend: 'print',
                title: function() {
                    return 'Product Items Report - ' + formattedDate;
                },
                text: 'Print Report',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                },
                customize: function(win) {
                    $(win.document.body).prepend('<img src="/Images/logo.png" style="width: 100px; height: auto; margin-bottom: 20px;"/>');
                    $(win.document.body).prepend('<h2 style="text-align: center; color: #FF6A13; font-family: Arial, sans-serif; font-weight: bold; font-size: 20px;">Product Items Report - ' + formattedDate + '</h2>');
                    $(win.document.body).find('th').css({
                        'background-color': '#FF6A13',
                        'color': 'white',
                        'font-weight': 'bold',
                        'font-size': '14px',
                        'text-align': 'center'
                    });
                    $(win.document.body).find('tr:nth-child(even)').css('background-color', '#FFEBE0');
                    $(win.document.body).find('tr:nth-child(odd)').css('background-color', '#FFE0CC');
                }
            }
        ],
        responsive: true,
        lengthChange: true,
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
        ],
        ordering: true,
        columnDefs: [
            { orderable: true, targets: [0, 1, 6, 7] }
        ]
    });

    // Add filter functionality to DataTable
    $('#filter_item_type').change(function () {
        var selectedType = $(this).val();
        // If "All" is selected, clear the search
        if (selectedType === 'All') {
            table.search('').draw();
        } else {
            // Use the DataTable search function to filter by category name
            table.column(2).search(selectedType).draw();  // Column 2 is the Item Type column
        }
    });
});

</script>




