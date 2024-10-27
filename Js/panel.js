//  reservation view
function closeModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.remove('show'); // Remove the "show" class to hide the modal
    modal.style.display = 'none';   // Hide the modal
    document.body.classList.remove('modal-open');  // Remove the body class that Bootstrap adds when the modal is open
    document.querySelector('.modal-backdrop')?.remove();  // Remove the backdrop if present
  
    // Refresh the reservation view after closing the modal
    showReservation();
  }
  
  // Function to manually show the modal
  function showModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.add('show');  // Add the "show" class to display the modal
    modal.style.display = 'block'; // Ensure it's displayed
    document.body.classList.add('modal-open');  // Add the body class to prevent background scroll
    const backdrop = document.createElement('div'); // Create a modal backdrop
    backdrop.className = 'modal-backdrop fade show';
    document.body.appendChild(backdrop);  // Append the backdrop to the body
  }
  
  // Example function to handle AJAX and show modal with success/error message
  function handleAjaxResponse(response) {
    if (response.status === 'success') {
      document.getElementById('modalTitle').textContent = 'Success';
      document.getElementById('modalBody').innerHTML = `<div class="alert alert-success">${response.message}</div>`;
    } else {
      document.getElementById('modalTitle').textContent = 'Error';
      document.getElementById('modalBody').innerHTML = `<div class="alert alert-danger">${response.message}</div>`;
    }
    showModal();
  }
  
  // Attach form submission event handler to all forms with the class 'reservation-form'
  document.addEventListener('submit', function (event) {
    if (event.target.matches('.reservation-form')) {
      event.preventDefault(); // Prevent default form submission
  
      const formData = new FormData(event.target); // Serialize form data
  
      // Send an AJAX request to the server to update the reservation
      fetch('/adminview/viewsReservation.php', {
        method: 'POST',
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => handleAjaxResponse(data))
        .catch((error) => {
          document.getElementById('modalTitle').textContent = 'Error';
          document.getElementById('modalBody').innerHTML = '<div class="alert alert-danger">An error occurred while updating the reservation.</div>';
          showModal();
          console.error('Error:', error);
        });
    }
  });
  
  // Function to refresh the reservation list dynamically
  function showReservation() {
    fetch('/adminview/viewsReservation.php', {
      method: 'POST',
    })
      .then((response) => response.text())
      .then((data) => {
        document.querySelector('.allContent-section').innerHTML = data; // Update the table content dynamically
      });
  }
  



function showActivity_log(searchTerm = '') {
    $.ajax({
        url: "adminview/viewActivity_log-.php",
        method: "post",
        data: { 
            record: 1,
            search: searchTerm // Pass the search term to the server-side
        },
        success: function(data) {
            $('.allContent-section').html(data); // Update the content with the filtered logs
        }
    });
}

// Attach event listener to search input
$(document).on('input', '#activityLogSearch', function() {
    let searchTerm = $(this).val();
    showActivity_log(searchTerm); // Call the function with the search term
});




function showUser() {
    $.ajax({
        url: "adminview/viewUser.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

function showOrders() {
    $.ajax({
        url: "adminview/viewAllOrders.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}


// AJAX to change order status
function ChangeOrderStatus(orderId, newStatus) {
    $.ajax({
        url: "/controls/updateOrderStatus.php",  // Assuming this PHP file handles the status update
        method: "POST",
        data: { record: orderId, new_status: newStatus },
        success: function(data) {
            if (data.trim() === "success") {
                alert('Order Status updated successfully');
                refreshOrderList();  // Dynamically refresh the order list
            } else {
                alert('Failed to update the order status');
            }
        },
        error: function() {
            alert('Error during the request');
        }
    });
}




// Refresh order list dynamically without redirecting
function refreshOrderList() {
    $.ajax({
        url: 'adminview/viewAllOrders.php',  // URL to load the order list
        method: 'GET',
        success: function(data) {
            $('.container').html(data);  // Update the content with the order list
        },
        error: function() {
            alert('Error refreshing the order list.');
        }
    });
}





// Add product data
function addItems() {
    var item_name = $('#item_name').val();
    var item_type = $('#item_type').val();
    var stock = $('#stock').val();
    var price = $('#price').val();
    var special_instructions = $('#special_instructions').val();
    var file = $('#item_image')[0].files[0];  // Ensure this matches the input field

    // Check if all fields are filled
    if (!item_name || !item_type || !stock || !price || !file) {
        alert("Please fill in all fields and select a file.");
        return;
    }

    var fd = new FormData();
    fd.append('item_name', item_name);
    fd.append('item_type', item_type);
    fd.append('stock', stock);
    fd.append('price', price);
    fd.append('special_instructions', special_instructions);
    fd.append('item_image', file);  // Add the file input
    fd.append('upload', '1'); // Add a flag to signify the upload process

    $.ajax({
        url: "/controls/addItemController.php",  // Ensure the correct path
        method: "POST",
        data: fd,
        processData: false,
        contentType: false,
        success: function(data) {
            console.log(data);  // Log the response for debugging
            $('#myModal').modal('hide');  // Hide the modal after success
            $('#productForm').trigger('reset');  // Reset the form fields
            
            // Show alert and refresh after the user clicks "OK"
            alert("Product added successfully.");
            setTimeout(function() {
                refreshProductList();  // Refresh the product list after OK
            });  // Short delay before refreshing the list
        },
        error: function(xhr, status, error) {
            console.error("Error: " + xhr.responseText);  // Log the error response
            alert("Error: Unable to add the product. Please try again.");
        }
    });
}




// Refresh product list dynamically without redirecting
function refreshProductList() {
    $.ajax({
        url: 'adminview/viewAllProducts.php',  // URL to load the product list
        method: 'GET',
        success: function(data) {
            $('.allContent-section').html(data);  // Update the content with the product list
        },
        error: function() {
            alert('Error refreshing the product list.');
        }
    });
}

// Function to show table views dynamically
function showTableViews() {
    $.ajax({
        url: "adminview/viewTable.php",  // Adjust this path to your actual view table script
        method: "POST",
        data: { record: 1 },  // Send any required data for server-side filtering (if needed)
        success: function(data) {
            $('.allContent-section').html(data);  // Update the HTML with table views
        },
        error: function() {
            alert("Error fetching table views.");
        }
    });
}

// Add table data with optional images (front, back, left, right)
function addTable() {
    var table_number = $('#table_number').val();
    var seating_capacity = $('#seating_capacity').val();
    var area = $('#area').val();
    var front_image = $('#front_image')[0].files[0];
    var back_image = $('#back_image')[0].files[0];
    var left_image = $('#left_image')[0].files[0];
    var right_image = $('#right_image')[0].files[0];

    // Check if all required fields are filled
    if (!table_number || !seating_capacity || !area) {
        alert("Please fill in all required fields.");
        return;
    }

    var fd = new FormData();
    fd.append('table_number', table_number);
    fd.append('seating_capacity', seating_capacity);
    fd.append('area', area);

    if (front_image) {
        fd.append('front_image', front_image); 
    }
    if (back_image) {
        fd.append('back_image', back_image);  
    }
    if (left_image) {
        fd.append('left_image', left_image);  
    }
    if (right_image) {
        fd.append('right_image', right_image); 
    }
    fd.append('upload', '1'); 

    $.ajax({
        url: "/controls/addtables.php",  // Ensure the correct path to your PHP file
        method: "POST",
        data: fd,
        processData: false,
        contentType: false,
        success: function(response) {
            try {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    alert("Table added successfully.");
                    $('#addTableModal').modal('hide');  
                    $('#tableForm').trigger('reset');  
                    setTimeout(function() {
                        refreshTableList(); 
                    }, 500); 
                } else {
                    alert("Error: " + data.message);
                }
            } catch (e) {
                console.error("Error parsing response:", e);
                alert("An error occurred while processing your request.");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + xhr.responseText); 
            alert("Error: Unable to add the table. Please try again.");
        }
    });
}

// Refresh table list dynamically without redirecting
function refreshTableList() {
    $.ajax({
        url: 'adminview/viewTable.php',  // URL to load the table list
        method: 'GET',
        success: function(data) {
            $('.allContent-section').html(data);  // Update the content with the table list
        },
        error: function() {
            alert('Error refreshing the table list.');
        }
    });
}




// Filter tables by area (Indoor/Outdoor)
function filterTables() {
    var area = $('#filter_area').val();

    $.ajax({
        url: 'adminview/viewTables.php',
        method: 'POST',
        data: { area: area },
        success: function(data) {
            $('.table-list').html(data);  // Update table list with filtered data
        },
        error: function() {
            alert("Error filtering tables.");
        }
    });
}





function showProductItems() {
    $.ajax({
        url: "adminview/viewAllProducts.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        },
        error: function() {
            alert("Error fetching product items.");
        }
    });
}

// Load the edit form for a product
function itemEditForm(id) {
    $.ajax({
        url: "adminview/editItemForm.php",  // URL to load the edit form
        method: "POST",
        data: { record: id },  // Send the product ID
        success: function(data) {
            $('.allContent-section').html(data);  // Load the form HTML into the section
        },
        error: function() {
            alert("Error loading the form.");
        }
    });
}


function updateItems(event) {
    event.preventDefault(); // Prevent default form submission

    var product_id = $('#product_id').val();
    var product_name = $('#product_name').val();
    var category_id = $('#category_id').val(); // category_id instead of item_type
    var quantity = $('#quantity').val();
    var price = $('#price').val();
    var special_instructions = $('#special_instructions').val();
    var product_image = $('#item_image')[0].files[0];

    if (!product_name || !category_id || !quantity || !price) {
        alert('All fields except special instructions and image are required.');
        return;
    }

    var fd = new FormData();
    fd.append('product_id', product_id);
    fd.append('product_name', product_name);
    fd.append('category_id', category_id); // Pass category_id
    fd.append('quantity', quantity);
    fd.append('price', price);
    fd.append('special_instructions', special_instructions);

    if (product_image) {
        fd.append('item_image', product_image);
    }

    $.ajax({
        url: '/controls/updateItemController.php',
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function (data) {
            try {
                var response = JSON.parse(data); 
                if (response.status === 'success') {
                    alert('Product updated successfully.');
                    refreshProductList(); 
                } else {
                    alert('Error: ' + response.message);
                }
            } catch (e) {
                alert("Error: Invalid response from the server.");
                console.error("Response:", data);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error: " + xhr.responseText);
            alert('Error updating the product item.');
        }
    });
}


// Function to refresh the product list without redirecting
function refreshProductList() {
    $.ajax({
        url: 'adminview/viewAllProducts.php',  // URL to load the product list
        method: 'GET',
        success: function(data) {
            $('.allContent-section').html(data);  // Update the content with the product list
        },
        error: function() {
            alert('Error refreshing the product list.');
        }
    });
}




// Delete product data 
function itemDelete(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        $.ajax({
            url: "/controls/deleteItemController.php",
            method: "POST",
            data: { record: id },
            success: function(data) {
                console.log(data);  // Log the response for debugging
                alert(data);  // Show success or error message
                showProductItems();  // Refresh the product list after deletion
            },
            error: function(xhr, status, error) {
                console.error("Error: " + xhr.responseText);  // Log any errors from the server
                alert("Error: Unable to delete the product.");
            }
        });
    }
}




function filterItems() {
    var selectedType = document.getElementById("filter_item_type").value;
    var rows = document.querySelectorAll(".product-row");

    rows.forEach(function(row) {
        var itemType = row.getAttribute("data-item-type");

        if (selectedType === "All" || itemType === selectedType) {
            row.style.display = "";  // Show the row
        } else {
            row.style.display = "none";  // Hide the row
        }
    });
}







$(document).ready(function() {
    // Automatically load all users when the page loads
    UfilterItems();  // Call this function to load all users by default
});

// Function to filter users based on search and date inputs or load all users
function UfilterItems() {
    var searchTerm = $('#searchInput').val();
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();

    $.ajax({
      url: 'controls/searchcontrol.php',  // Server-side PHP script to fetch filtered or all users
      method: 'GET',
      data: {
        search: searchTerm,       // Pass search term (empty by default)
        start_date: startDate,    // Pass start date (empty by default)
        end_date: endDate         // Pass end date (empty by default)
      },
      success: function(response) {
        $('#usersTableBody').html(response);  // Populate the table body with all users

        // Call filterItems() one more time after successful load
        setTimeout(function() {
            filterItemsAgain();  // Call the function again after successful load
        }, 100);  // Adjust the delay if needed
      },
      error: function(xhr, status, error) {
        alert('Error loading users. Please try again.');
        console.error('AJAX error:', status, error);
      }
    });
}

// This function will be triggered after the first successful data load
function filterItemsAgain() {
    // Optionally add logic here if the second load needs to be different
    // For now, we just call filterItems again
    UfilterItems();
}

// Clear filters and reload all users
function clearFilters() {
    $('#searchInput').val('');  // Clear search input
    $('#startDate').val('');    // Clear start date
    $('#endDate').val('');      // Clear end date

    // Reload the table with all users by calling filterItems without filters
    UfilterItems();
}






// Load the edit form for a table
function tableEditForm(id) {
    $.ajax({
        url: "adminview/editables-forms.php",  // URL to load the edit form for tables
        method: "POST",
        data: { record: id },  // Send the table ID
        success: function(data) {
            $('.allContent-section').html(data);  // Load the form HTML into the section
        },
        error: function() {
            alert("Error loading the form.");
        }
    });
}

// Update table data with image handling
function updateTables(event) {
    event.preventDefault(); // Prevent default form submission

    // Get the values from the form fields
    var table_id = document.getElementById('table_id').value;
    var table_number = document.getElementById('table_number').value;
    var seating_capacity = document.getElementById('seating_capacity').value;
    var area = document.getElementById('area').value;
    var is_available = document.getElementById('is_available').value;

    // Collect file inputs for different views
    var back_view = document.getElementById('back_view').files[0];
    var left_view = document.getElementById('left_view').files[0];
    var right_view = document.getElementById('right_view').files[0];
    var front_view = document.getElementById('front_view').files[0];

    // Create FormData object to handle file uploads and form fields
    var fd = new FormData();
    fd.append('table_id', table_id);
    fd.append('table_number', table_number);
    fd.append('seating_capacity', seating_capacity);
    fd.append('area', area);
    fd.append('is_available', is_available);

    // Append the image files if they are uploaded
    if (back_view) fd.append('new_image_back_view', back_view);
    if (left_view) fd.append('new_image_left_view', left_view);
    if (right_view) fd.append('new_image_right_view', right_view);
    if (front_view) fd.append('new_image_front_view', front_view);

    // Send the AJAX request to update the table
    $.ajax({
        url: 'controls/updateTableController.php',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function (data) {
            try {
                // Parse the response
                var response = JSON.parse(data);
                
                // Handle success case
                if (response.status === 'success') {
                    alert('Table updated successfully.');
                    refreshUpdateTableList(); // Optionally refresh the table list or UI here
                } else {
                    // Handle error cases
                    alert('Error: ' + response.message);
                }
            } catch (e) {
                // Handle cases where the server response is not JSON
                alert("Error: Invalid response from the server.");
                console.error("Response:", data); // Log the response for debugging
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error: ", status, error);
            alert('There was an error updating the table.');
        }
    });
}


// Function to refresh the table list
function refreshUpdateTableList() {
    $.ajax({
        url: 'adminview/viewTable.php',  // URL to load the table list
        method: 'GET',
        success: function(data) {
            $('.allContent-section').html(data);  // Update the content with the table list
        },
        error: function() {
            alert('Error refreshing the table list.');
        }
    });
}

function deleteTable(table_id) {
    if (confirm('Are you sure you want to delete this table?')) {
        // Disable the delete button and show a loading spinner (optional, for better UX)
        const button = document.querySelector(`button[onclick="deleteTable('${table_id}')"]`);
        button.disabled = true;
        button.innerHTML = 'Deleting...';

        $.ajax({
            url: '/controls/deletetableController.php',  // Adjust the path to match your setup
            method: 'POST',
            data: { table_id: table_id },
            success: function(response) {
                alert(response);  // Show the response from the server
                refreshTableList();  // Reload the page to refresh the table list
            },
            error: function(xhr, status, error) {
                console.error('Error: ' + xhr.responseText);  // Log any errors from the server
                alert('Error: Unable to delete the table.');
                // Re-enable the button if there is an error
                button.disabled = false;
                button.innerHTML = 'Delete';
            }
        });
    }
}







function eachDetailsForm(id) {
    $.ajax({
        url: "adminview/viewEachDetails.php",
        method: "post",
        data: { record: id },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}









// Update variation after submit
function updateVariations() {
    var v_id = $('#v_id').val();
    var product = $('#product').val();
    var size = $('#size').val();
    var qty = $('#qty').val();
    
    var fd = new FormData();
    fd.append('v_id', v_id);
    fd.append('product', product);
    fd.append('size', size);
    fd.append('qty', qty);
    
    $.ajax({
        url: 'controller/updateVariationController.php',
        method: 'post',
        data: fd,
        processData: false,
        contentType: false,
        success: function(data) {
            alert('Update Success.');
            $('form').trigger('reset');
            showProductSizes();
        }
    });
}



function quantityPlus(id) { 
    $.ajax({
        url: "controller/addQuantityController.php",
        method: "post",
        data: { record: id },
        success: function(data) {
            $('form').trigger('reset');
            showMyCart();
        }
    });
}

function quantityMinus(id) {
    $.ajax({
        url: "controller/subQuantityController.php",
        method: "post",
        data: { record: id },
        success: function(data) {
            $('form').trigger('reset');
            showMyCart();
        }
    });
}

function checkout() {
    $.ajax({
        url: "view/viewCheckout.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

function showCategory() {
    $.ajax({
        url: "adminview/viewAllCategory.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        },
        error: function() {
            alert("Error fetching product items.");
        }
    });
}

// Add Category function
function addCategory() {
    var categoryName = document.getElementById('add_category_name').value.trim();  // Get the input value

    // Validate the input for the category name
    if (categoryName === '') {
        alert('Category name is required.');
        return;
    }

    // AJAX request to add category
    $.ajax({
        url: '/controls/addCatController.php',  // URL to your PHP controller that handles category addition
        type: 'POST',
        dataType: 'json',  // Expect JSON response
        data: { category_name: categoryName },  // Send the category name to the server
        success: function(response) {
            if (response.success) {
                $('#categoryModal').modal('hide');  // Hide the modal after successful addition
                refreshCategoryList();  // Refresh the category list dynamically
            } else {
                alert(response.message || 'An error occurred while adding the category.');  // Display the error message
            }
        },
        error: function(xhr, status, error) {
            alert('An unexpected error occurred: ' + error);  // Handle server errors
        }
    });
}


// Load the edit form for a category
function categoryEditForm(id) {
    $.ajax({
        url: "adminview/editcategory-form.php",  // URL to load the edit form for categories
        method: "POST",
        data: { record: id },  // Send the category ID
        success: function(data) {
            // Replace the current HTML content with the edit form (loaded via AJAX)
            $('.allContent-section').html(data);  // Load the form HTML into the section
        },
        error: function() {
            alert("Error loading the form.");
        }
    });
}
// Update category data without image handling
function updateCategory(event) {
    event.preventDefault(); // Prevent default form submission

    // Get the values from the form fields
    var category_id = document.getElementById('category_id').value;
    var category_name = document.getElementById('category_name').value;

    // Create FormData object to handle form fields
    var fd = new FormData();
    fd.append('category_id', category_id);
    fd.append('category_name', category_name);

    // Send the AJAX request to update the category
    $.ajax({
        url: 'controls/updatecatController.php',  // URL to your update controller
        type: 'POST',
        data: fd,
        processData: false,  // Prevent jQuery from automatically transforming the data into a query string
        contentType: false,  // Required to send form data
        success: function (data) {
            try {
                // Parse the response
                var response = JSON.parse(data);

                // Handle success case
                if (response.success) {
                    alert('Category updated successfully.');
                    showCategory();  // Call showCategory() to load the updated category list
                } else {
                    // Handle error cases
                    alert('Error: ' + response.message);
                }
            } catch (e) {
                // Handle cases where the server response is not JSON
                alert("Error: Invalid response from the server.");
                console.error("Response:", data); // Log the response for debugging
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error: ", status, error);
            alert('There was an error updating the category.');
        }
    });
}


// Delete Category function
function categoryDelete(categoryId) {
    if (confirm('Are you sure you want to delete this category?')) {
        // AJAX request to delete category
        $.ajax({
            url: '/controls/deletecatController.php',  // The URL to your delete category script
            type: 'POST',
            dataType: 'json',  // Expect a JSON response from the server
            data: { category_id: categoryId },
            success: function(response) {
                if (response.success) {
                    console.log(response);  // Log the response for debugging
                    alert(response.message);  // Show the success message returned by the server
                    refreshCategoryList()  // Refresh the product list after deletion
                } else {
                    alert(response.message);  // Display error message returned by the server
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);  // Handle server errors
            }
        });
    }
}







  
// Function to refresh the entire category list section dynamically
function refreshCategoryList() {
    $.ajax({
        url: 'adminview/viewAllCategory.php',  // URL to load the updated category list content
        method: 'GET',
        success: function(data) {
            $('.category-list').html($(data).find('.category-list').html());  // Replace the category list content dynamically
        },
        error: function() {
            alert('Error refreshing the category list.');
        }
    });
}


