
// General AJAX function for handling content updates
function updateContent(url, data, targetSelector) {
    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        success: function (response) {
            $(targetSelector).html(response);
        },
        error: function (xhr, status, error) {
            console.error(`Error loading content from ${url}:`, error);
            alert(`Error loading content. Please try again.`);
        }
    });
}



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
      fetch('/Ownerview/viewsReservation.php', {
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
  


// Function to refresh the reservation list dynamically using AJAX
function showReservation() {
    $.ajax({
        url: 'Ownerview/viewsReservation.php',  // The PHP file to call
        type: 'POST',                          // Send data using POST method
        data: { record: 1 },                   // The data to send (record = 1)
        success: function(data) {
            // On success, update the content of the .allContent-section
            $('.allContent-section').html(data);
        },
        error: function(xhr, status, error) {
            // Handle errors if any
            console.error('Error fetching reservation data:', error);
        }
    });
}

  

  function showActivity_log() {
    $.ajax({
        url: "Ownerview/viewActivity_log-.php",
        method: "POST",
        data: { record: 1 },                   // The data to send (record = 1)
        success: function(data) {
            $('.allContent-section').html(data);
        },
        error: function(xhr, status, error) {
            console.error("Error fetching activity logs:", error);
            alert("An error occurred while loading activity logs.");
        }
    });
}

// Function to show Admin/Staff management view
function showadmin() {
    $.ajax({
        url: "Ownerview/adminmanagement.php",
        method: "POST",
        success: function(data) {
            $('.allContent-section').html(data);  // Load the admin management page into the section
        }
    });
}
function addAdmin() {
    var formData = $('#adminForm').serialize();  // Serialize form data

    $.ajax({
        url: '/Ocontrols/addAdmin.php',  // Backend script to handle adding
        method: 'POST',
        data: formData,
        dataType: 'json',  // Expect a JSON response from the server
        success: function(response) {
            if (response.status === 'success') {
                $('#adminModal').modal('hide');  // Close the modal after successful addition
                alert(response.message);  // Show success message
                showadmin();  // Load the admin management page after adding a new user
            } else {
                alert(response.message);  // Show error message if email or username already exists
            }
        },
        error: function(xhr, status, error) {
            console.log("AJAX error: " + xhr.responseText);  // Log error to the console for debugging
            alert('Error adding Admin/Staff');
        }
    });
}


// Function to show the edit form for Admin/Staff
function adminEditForm(userId) {
    // Confirmation prompt before loading the edit form
    if (!confirm("Are you sure you want to edit this user's details?")) {
        alert("Edit canceled.");
        return; // Exit the function if the user cancels
    }

    // Password prompt for security confirmation
    const password = prompt("Please enter your password to confirm editing:");
    if (!password) {
        alert("Edit canceled. Password is required.");
        return; // Exit the function if no password is entered
    }

    // Proceed to load the edit form if confirmed and password is provided
    $.ajax({
        url: "Ownerview/editAdminForm.php", // URL to load the edit form
        method: "POST",
        dataType: "json",
        data: { user_id: userId, user_password: password },
        success: function(response) {
            if (response.status === "success") {
                // Load the form HTML into the modal container
                $("#editAdminFormContainer").html(response.form_html);
                // Show the modal
                $("#editAdminModal").modal("show");
            } else {
                // Display the error message from the server response
                alert(response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error loading the form:", status, error); // Log the error
            alert("Error loading the form. Please try again."); // Show an alert for the error
        }
    });
}

function updateadmin(userId) {
    // Get the form element
    const form = document.getElementById('editAdminForm');

    // Create a FormData object from the form
    const formData = new FormData(form);

    // Add the user ID to the FormData object
    formData.append('user_id', userId);

    // Use the Fetch API to send the form data to the server
    fetch('/Ocontrols/updateAdmin.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Ensure the server responds with a valid status
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`Network error: ${response.status} - ${text}`);
            });
        }
        return response.json(); // Parse the JSON response
    })
    .then(data => {
        // Handle the server's JSON response
        if (data.status === 'success') {
            alert(data.message); // Show success message from the server
            // Optionally refresh the UI
            $("#editAdminModal").modal("hide");
        } else {
            // Handle server error messages
            alert('Error updating user: ' + (data.message || 'Unknown error.'));
        }
    })
    .catch(error => {
        // Log and display any errors that occurred
        console.error('Error during update operation:', error);
        alert('An error occurred while updating the user. Please check your network or try again.');
    });
}



function adminDelete(userId) {
    // Prompt the logged-in user for their password before proceeding with deletion
    const password = prompt("Please enter your password to confirm deletion:");
    if (!password) {
        // User canceled the prompt or left it blank
        alert("Deletion canceled. Password is required.");
        return;
    }

    if (confirm('Are you sure you want to delete this user?')) {  // Confirm deletion
        $.ajax({
            url: '/Ocontrols/deleteAdmin.php',
            method: 'POST',
            data: { user_id: userId, user_password: password },  // Send the target user ID and the logged-in user's password
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    refreshAdminList();  // Refresh the list without a page reload
                } else {
                    // Handle error cases (incorrect password, etc.)
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", status, error);
                alert('An error occurred while trying to delete the user.');
            }
        });
    }
}




function refreshAdminList() {
    $.ajax({
        url: 'Ownerview/adminmanagement.php',  // PHP script that returns updated rows
        method: 'GET',
        success: function(data) {
            $('.admin-table').html(data);  // Update the table body with the new data
        },
        error: function() {
            alert('Error refreshing the list.');  // Show error if there's an issue
        }
    });
}





function showUser() {
    $.ajax({
        url: "Ownerview/viewUser.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

function showOrders() {
    $.ajax({
        url: "Ownerview/viewAllOrders.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}


function ChangeOrderStatus(orderId, newStatus) {
    $.ajax({
        url: "/Ocontrols/updateOrderStatus.php",
        method: "POST",
        data: { record: orderId, new_status: newStatus },
        success: function(response) {
            response = response.trim();
            if (response === "success") {
                alert('Order Status updated successfully');
                
                // Update the button text immediately
                const dropdownButton = document.querySelector(`#order-status-button-${orderId}`);
                if (dropdownButton) {
                    dropdownButton.textContent = newStatus;
                }

                // Close the modal after successful update
                $('#viewModal').modal('hide'); // Close the modal using the correct ID
                
                // Ensure the backdrop is hidden
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();

                // Dynamically refresh the orders list
                refreshOrderList();
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
        url: 'Ownerview/viewAllOrders.php',  // Adjust the URL if needed
        method: 'GET',
        success: function(response) {
            $('.allContent-section').html(response);  // Replace the content in the section
        },
        error: function() {
            alert('Error refreshing the orders list');
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
        url: "/Ocontrols/addItemController.php",  // Ensure the correct path
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
    updateContent('Ownerview/viewAllProducts.php', {}, '.allContent-section');
}

// Function to show table views dynamically
function showTableViews() {
    $.ajax({
        url: "Ownerview/viewTable.php",  // Adjust this path to your actual view table script
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
        url: "/Ocontrols/addtables.php",  // Ensure the correct path to your PHP file
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
    updateContent('Ownerview/viewTable.php', {}, '.allContent-section');

}




function showProductItems() {
    $.ajax({
        url: "Ownerview/viewAllProducts.php",
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
// Load the edit form for a product
// Load the edit form for a product inside the modal
function itemEditForm(id) {
    $.ajax({
        url: "Ownerview/editItemForm.php",  // URL to load the edit form
        method: "POST",
        data: { record: id },  // Send the product ID
        success: function(data) {
            // Load the form HTML into the modal body
            $('#editProductContent').html(data);

            // Show the modal after the content is loaded
            $('#editModal').modal('show');
        },
        error: function() {
            alert("Error loading the form.");
        }
    });
}

// Update items
function updateItems(event) {
    event.preventDefault(); // Prevent default form submission

    // Get the form element
    const form = document.getElementById('update-Items');

    // Create a FormData object from the form
    const formData = new FormData(form);

    // Get the product_id from the form and append it to the FormData object
    const product_id = document.getElementById('product_id').value;
    formData.append('product_id', product_id);

    // Send the form data via AJAX
    $.ajax({
        url: '/Ocontrols/updateItemController.php',  // The URL to your update handler
        method: 'POST',
        data: formData,
        processData: false,  // Prevent jQuery from processing the data
        contentType: false,  // Prevent jQuery from setting contentType (needed for FormData)
        success: function(data) {
            try {
                var response = JSON.parse(data);  // Parse the server response
                if (response.status === 'success') {
                    alert('Product updated successfully.');
                    
                    // Hide the modal after success
                    $('#editModal').modal('hide');  // Bootstrap hides the modal

                    // Manually remove the modal backdrop and close classes to prevent the grey overlay
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');

                    refreshProductList();  // Call your function to refresh the product list

                    // Optional: Clear modal content (reset form) if necessary
                    $('#editProductContent').html('');
                } else {
                    alert('Error: ' + response.message);
                }
            } catch (e) {
                alert("Error: Invalid response from the server.");
                console.error("Response:", data);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + xhr.responseText);
            alert('Error updating the product item.');
        }
    });
}









// Function to refresh the product list without redirecting
function refreshProductList() {
    $.ajax({
        url: 'Ownerview/viewAllProducts.php',  // URL to load the product list
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
            url: "/Ocontrols/deleteItemController.php",
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





$(document).ready(function () {
    // Automatically load all users when the page loads
    UfilterItems();
});


// Function to filter users based on search and date inputs or load all users
function UfilterItems() {
    var searchTerm = $('#searchInput').val();
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();

    $.ajax({
      url: '/Ocontrols/searchcontrol.php',  // Server-side PHP script to fetch filtered or all users
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
        url: "Ownerview/editables-forms.php",  // URL to load the edit form for tables
        method: "POST",
        data: { record: id },  // Send the table ID
        success: function(data) {
            $('#editTableContent').html(data);  // Load the form HTML into the modal body
            $('#editTableModal').modal('show');  // Show the modal once the content is loaded
        },
        error: function() {
            alert("Error loading the form.");
        }
    });
}

// Update table data with image handling
function updateTables(event) {
    event.preventDefault(); // Prevent default form submission

    // Get the form element
    const form = document.getElementById('update-Table'); // Ensure this matches the form's ID

    // Create a FormData object from the form
    const formData = new FormData(form);

    // Get the product_id from the form and append it to the FormData object
    const product_id = document.getElementById('table_id').value;
    formData.append('table_id', product_id);

    // Collect file inputs for different views
    var back_view = document.getElementById('back_view').files[0];
    var left_view = document.getElementById('left_view').files[0];
    var right_view = document.getElementById('right_view').files[0];
    var front_view = document.getElementById('front_view').files[0];

    // Append the image files to FormData if they are uploaded
    if (back_view) formData.append('new_image_back_view', back_view);
    if (left_view) formData.append('new_image_left_view', left_view);
    if (right_view) formData.append('new_image_right_view', right_view);
    if (front_view) formData.append('new_image_front_view', front_view);

    // Send the AJAX request to update the table
    $.ajax({
        url: '/Ocontrols/updateTableController.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            try {
                // Parse the response
                var response = JSON.parse(data);

                // Handle success case
                if (response.status === 'success') {
                    alert('Table updated successfully.');

                    // Hide the modal after success
                    $('#editTableModal').modal('hide');  // Bootstrap hides the modal

                    // Manually remove the modal backdrop and close classes to prevent the grey overlay
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');

                    // Optionally refresh the table list or UI here
                    refreshUpdateTableList(); 
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
        url: 'Ownerview/viewTable.php',  // URL to load the table list
        method: 'GET',
        success: function(data) {
            $('.allContent-section').html(data);  // Update the content with the table list
        },
        error: function() {
            alert('Error refreshing the table list.');
        }
    });
}

// delete table
function deleteTable(table_id) {
    if (confirm('Are you sure you want to delete this table?')) {
        // Disable the delete button and show a loading spinner (optional, for better UX)
        const button = document.querySelector(`button[onclick="deleteTable('${table_id}')"]`);
        button.disabled = true;
        button.innerHTML = 'Deleting...';

        $.ajax({
            url: '/Ocontrols/deletetableController.php',  // Adjust the path to match your setup
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


// show category
function showCategory() {
    $.ajax({
        url: "Ownerview/viewAllCategory.php",
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





// Load the edit form for a category
function categoryEditForm(id) {
    $.ajax({
        url: "Ownerview/editcategory-form.php",  // URL to load the edit form for categories
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


// Add Category function
function addCategory() {
    var categoryName = document.getElementById('add_category_name').value.trim(); // Get the input value

    // Validate the input for the category name
    if (categoryName === '') {
        alert('Category name is required.');
        return;
    }

    // AJAX request to add category
    $.ajax({
        url: '/Ocontrols/addCatController.php', // URL to your PHP controller
        type: 'POST',
        dataType: 'json', // Expect JSON response
        data: { category_name: categoryName }, // Send the category name to the server
        success: function(response) {
            if (response.success) {
                // Clear the input field
                document.getElementById('add_category_name').value = '';

                // Hide the modal after successful addition
                $('#categoryModal').modal('hide');
                $('.modal-backdrop').remove();  // Remove the backdrop
                $('#categoryModal').data('bs.modal', null);  // Reset modal state

                // Display a success message
                alert('Category added successfully.');

                // Refresh the category list dynamically
                refreshCategoryList();
            } else {
                // Display the error message from the server
                alert(response.message || 'An error occurred while adding the category.');
            }
        },
        error: function(xhr, status, error) {
            alert('An unexpected error occurred: ' + error); // Handle server errors
        }
    });
}


// Update catesgory data without image handling
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
        url: '/Ocontrols/updatecatController.php',  // URL to your update controller
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
                    $('#editModal').modal('hide');  // Hide the modal
                    $('.modal-backdrop').remove();  // Remove the backdrop
                    $('#editModal').data('bs.modal', null);  // Reset modal state
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



function categoryDelete(categoryId) {
    if (confirm('Are you sure you want to delete this category?')) {
        $.ajax({
            url: '/Ocontrols/deletecatController.php',
            type: 'POST',
            dataType: 'json',
            data: { category_id: categoryId },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    refreshCategoryList();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);
            }
        });
    }
}








  
// Function to refresh the entire category list section dynamically
function refreshCategoryList() {
    updateContent("Ownerview/viewAllCategory.php", {}, '.allContent-section');
}


function showmessage() {
    $.ajax({
        url: "Ownerview/message.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        },
        error: function() {
            alert("Error.");
        }
    });
}
