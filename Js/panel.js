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
  


// Function to refresh the reservation list dynamically using AJAX
function showReservation() {
    $.ajax({
        url: 'adminview/viewsReservation.php',  // The PHP file to call
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
        url: "adminview/viewActivity_log-.php",
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
        url: "adminview/adminmanagement.php",
        method: "POST",
        success: function(data) {
            $('.allContent-section').html(data);  // Load the admin management page into the section
        }
    });
}

function addAdmin() {
    var formData = $('#adminForm').serialize();  // Serialize form data

    // Show loading state
    Swal.fire({
        title: 'Adding Admin...',
        text: 'Please wait while we process your request.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/controls/addAdmin.php',  // Backend script to handle adding
        method: 'POST',
        data: formData,
        dataType: 'json',  // Expect a JSON response from the server
        success: function(response) {
            if (response.status === 'success') {
                // Hide the modal
                $('#adminModal').modal('hide');
                $('#adminForm').trigger('reset');

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    showadmin();  // Load the admin management page after adding a new user
                });
            } else {
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to add admin.',
                    footer: 'Please check the form and try again.'
                });
            }
        },
        error: function(xhr, status, error) {
            console.log("AJAX error: " + xhr.responseText);  // Log error to the console for debugging
            
            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Unable to add Admin/Staff.',
                footer: 'Please check your connection or try again later.'
            });
        }
    });
}


// Function to show the edit form for Admin/Staff
// ... existing code ...

function adminEditForm(userId) {
    // First confirmation using SweetAlert2
    Swal.fire({
        title: 'Confirm Edit',
        text: "Are you sure you want to edit this user's details?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, edit it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Password prompt using SweetAlert2
            Swal.fire({
                title: 'Password Required',
                input: 'password',
                inputLabel: 'Please enter your password to confirm editing',
                inputPlaceholder: 'Enter your password',
                showCancelButton: true,
                confirmButtonText: 'Proceed',
                cancelButtonText: 'Cancel',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                showLoaderOnConfirm: true,
                preConfirm: (password) => {
                    if (!password) {
                        Swal.showValidationMessage('Password is required');
                        return false;
                    }
                    return password;
                }
            }).then((passwordResult) => {
                if (passwordResult.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Loading...',
                        text: 'Please wait while we load the edit form.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Proceed with AJAX call if password was provided
                    $.ajax({
                        url: "adminview/editAdminForm.php",
                        method: "POST",
                        dataType: "json",
                        data: { 
                            user_id: userId, 
                            user_password: passwordResult.value 
                        },
                        success: function(response) {
                            Swal.close(); // Close the loading dialog

                            if (response.status === "success") {
                                // Load the form HTML into the modal container
                                $("#editAdminFormContainer").html(response.form_html);
                                // Show the modal
                                $("#editAdminModal").modal("show");
                            } else {
                                // Show error message using SweetAlert2
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to load edit form.',
                                    footer: 'Please check your password and try again.'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error loading the form:", status, error);
                            
                            // Show error message using SweetAlert2
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Unable to load the edit form.',
                                footer: 'Please try again or contact support if the problem persists.'
                            });
                        }
                    });
                }
            });
        }
    });
}

// ... rest of the code ...

function updateadmin(userId) {
    // Get the form element
    const form = document.getElementById('editAdminForm');

    // Create a FormData object from the form
    const formData = new FormData(form);

    // Add the user ID to the FormData object
    formData.append('user_id', userId);

    // Show loading state
    Swal.fire({
        title: 'Updating Admin...',
        text: 'Please wait while we process your request.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // Use the Fetch API to send the form data to the server
    fetch('/controls/updateAdmin.php', {
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
            // Hide the modal
            $("#editAdminModal").modal("hide");
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                showadmin(); // Refresh the admin list
            });
        } else {
            // Handle server error messages
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to update admin.',
                footer: 'Please try again or contact support if the problem persists.'
            });
        }
    })
    .catch(error => {
        // Log and display any errors that occurred
        console.error('Error during update operation:', error);
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Unable to update the admin.',
            footer: 'Please check your connection or try again later.'
        });
    });
}



// ... existing code ...

function adminDelete(userId) {
    // First prompt for password using SweetAlert2
    Swal.fire({
        title: 'Password Required',
        input: 'password',
        inputPlaceholder: 'Enter your password',
        inputAttributes: {
            autocapitalize: 'off',
            autocorrect: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Proceed',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: (password) => {
            if (!password) {
                Swal.showValidationMessage('Password is required');
                return false;
            }
            return password;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Second confirmation for deletion
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((deleteConfirm) => {
                if (deleteConfirm.isConfirmed) {
                    // Send delete request immediately without showing loading state
                    $.ajax({
                        url: '/controls/deleteAdmin.php',
                        method: 'POST',
                        data: { 
                            user_id: userId, 
                            user_password: result.value 
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                refreshAdminList(); // Refresh immediately after successful deletion
                                // Show success message after refresh
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete user.',
                                    footer: 'Please check your password and try again.'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", status, error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while trying to delete the user.',
                                footer: 'Please try again or contact support if the problem persists.'
                            });
                        }
                    });
                }
            });
        }
    });
}

// ... rest of the code ...



function refreshAdminList() {
    $.ajax({
        url: 'adminview/adminmanagement.php',  // PHP script that returns updated rows
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
        method: "POST",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("Error fetching product items: " + textStatus);
            console.error("Error details:", errorThrown); // {{ edit_2: Log error details for debugging }}
        }
    });
}
function ChangeOrderStatus(orderId, newStatus) {
    $.ajax({
        url: "/controls/updateOrderStatus.php",
        method: "POST",
        data: { record: orderId, new_status: newStatus },
        success: function(response) {
            response = response.trim();
            if (response === "success") {
                // Show success message using SweetAlert2
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Order Status updated successfully',
                    showConfirmButton: false,
                    timer: 1500
                });

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
                // Show error message using SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update the order status'
                });
            }
        },
        error: function() {
            // Show error message using SweetAlert2
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error during the request'
            });
        }
    });
}

// Refresh order list dynamically without redirecting
function refreshOrderList() {
    $.ajax({
        url: 'adminview/viewAllOrders.php',  // Adjust the URL if needed
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
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please fill in all fields and select a file.'
        });
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
            
            // Show success message and refresh after the user clicks "OK"
            Swal.fire({
                icon: 'success',
                title: 'Product Added',
                text: 'Product added successfully.'
            }).then(() => {
                refreshProductList();  // Refresh the product list after OK
            });
        },
        error: function(xhr, status, error) {
            console.error("Error: " + xhr.responseText);  // Log the error response
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Unable to add the product. Please try again.'
            });
        }
    });
}




// Refresh product list dynamically without redirecting
function refreshProductList() {
    updateContent('adminview/viewAllProducts.php', {}, '.allContent-section');
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
        Swal.fire({
            icon: 'warning',
            title: 'Required Fields',
            text: 'Please fill in all required fields.'
        });
        return;
    }

    // Show loading state
    Swal.fire({
        title: 'Adding Table...',
        text: 'Please wait while we process your request.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

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
        url: "/controls/addtables.php",
        method: "POST",
        data: fd,
        processData: false,
        contentType: false,
        success: function(response) {
            try {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    // Hide the modal
                    $('#addTableModal').modal('hide');
                    $('#tableForm').trigger('reset');

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Table added successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        refreshTableList();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to add table.',
                        footer: 'Please try again or contact support if the problem persists.'
                    });
                }
            } catch (e) {
                console.error("Error parsing response:", e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid response from the server.',
                    footer: 'Please check your connection or contact support.'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Unable to add the table.',
                footer: 'Please check your connection or try again later.'
            });
        }
    });
}

// ... rest of the code ...

// Refresh table list dynamically without redirecting
function refreshTableList() {
    updateContent('adminview/viewTable.php', {}, '.allContent-section');

}




function showProductItems() {
    $.ajax({
        url: "adminview/viewAllProducts.php",
        method: "POST",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
            // {{ edit_1: Add any additional success handling logic here }}
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("Error fetching product items: " + textStatus);
            console.error("Error details:", errorThrown); // {{ edit_2: Log error details for debugging }}
        }
    });
}


// Load the edit form for a product inside the modal
function itemEditForm(id) {
    $.ajax({
        url: "adminview/editItemForm.php",  // URL to load the edit form
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
// Update items
function updateItems(event) {
    event.preventDefault();

    const form = document.getElementById('update-Items');
    const formData = new FormData(form);
    const product_id = document.getElementById('product_id').value;
    formData.append('product_id', product_id);

    // Show loading state
    Swal.fire({
        title: 'Updating Product...',
        text: 'Please wait while we process your request.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/controls/updateItemController.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            try {
                var response = JSON.parse(data);
                if (response.status === 'success') {
                    // Hide the modal
                    $('#editModal').modal('hide');
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    $('#editProductContent').html('');

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Product updated successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        refreshProductList();
                    });
                } else if (response.status === 'no_changes') {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Changes Made',
                        text: 'No changes were detected in the product details.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Optionally close the modal if you want
                        $('#editModal').modal('hide');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to update product.',
                        footer: 'Please try again or contact support if the problem persists.'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid response from the server.',
                    footer: 'Please check your connection or contact support.'
                });
                console.error("Response:", data);
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Unable to update the product.',
                footer: 'Please check your connection or try again later.'
            });
            console.error("Error: " + xhr.responseText);
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




// Delete product data with SweetAlert2
function itemDelete(id) {
    // Show confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the product.',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send delete request
            $.ajax({
                url: "/controls/deleteItemController.php",
                method: "POST",
                data: { record: id },
                success: function(data) {
                    console.log(data);  // Log the response for debugging
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'The product has been deleted.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        showProductItems();  // Refresh the product list after deletion
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error: " + xhr.responseText);
                    
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to delete the product.',
                        footer: 'Please try again or contact support if the problem persists.'
                    });
                }
            });
        }
    });
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
      url: '/controls/searchcontrol.php',  // Server-side PHP script to fetch filtered or all users
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
            $('#editTableContent').html(data);  // Load the form HTML into the modal body
            $('#editTableModal').modal('show');  // Show the modal once the content is loaded
        },
        error: function() {
            alert("Error loading the form.");
        }
    });
}

function updateTables(event) {
    event.preventDefault();

    const form = document.getElementById('update-Table');
    const formData = new FormData(form);
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

    // Show loading state
    Swal.fire({
        title: 'Updating Table...',
        text: 'Please wait while we process your request.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/controls/updateTableController.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            try {
                var response = JSON.parse(data);

                if (response.status === 'success') {
                    // Hide the modal
                    $('#editTableModal').modal('hide');
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Table updated successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        refreshUpdateTableList();
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to update table.',
                        footer: 'Please try again or contact support if the problem persists.'
                    });
                }
            } catch (e) {
                console.error("Response:", data);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid response from the server.',
                    footer: 'Please check your connection or contact support.'
                });
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Unable to update the table.',
                footer: 'Please check your connection or try again later.'
            });
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

// delete table
// delete table
function deleteTable(table_id) {
    // Show confirmation dialog using SweetAlert2
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Disable the delete button and show loading state
            const button = document.querySelector(`button[onclick="deleteTable('${table_id}')"]`);
            button.disabled = true;
            button.innerHTML = 'Deleting...';

            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the table.',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/controls/deletetableController.php',
                method: 'POST',
                data: { table_id: table_id },
                success: function(response) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        refreshTableList();  // Refresh the table list after deletion
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + xhr.responseText);
                    
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to delete the table.',
                        footer: 'Please try again or contact support if the problem persists.'
                    });

                    // Re-enable the button if there is an error
                    button.disabled = false;
                    button.innerHTML = 'Delete';
                }
            });
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


// show category
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


// Add Category function
function addCategory() {
    var categoryName = document.getElementById('add_category_name').value.trim();

    // Validate the input for the category name
    if (categoryName === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Required Field',
            text: 'Category name is required.',
        });
        return;
    }

    // AJAX request to add category
    $.ajax({
        url: '/controls/addCatController.php',
        type: 'POST',
        dataType: 'json',
        data: { category_name: categoryName },
        success: function(response) {
            if (response.success) {
                // Clear the input field
                document.getElementById('add_category_name').value = '';

                // Hide the modal after successful addition
                $('#categoryModal').modal('hide');
                $('.modal-backdrop').remove();
                $('#categoryModal').data('bs.modal', null);

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Category added successfully.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    refreshCategoryList();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'An error occurred while adding the category.'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An unexpected error occurred: ' + error
            });
        }
    });
}

// Update category data
function updateCategory(event) {
    event.preventDefault();

    var category_id = document.getElementById('category_id').value;
    var category_name = document.getElementById('category_name').value;

    var fd = new FormData();
    fd.append('category_id', category_id);
    fd.append('category_name', category_name);

    // Show loading state
    Swal.fire({
        title: 'Updating...',
        text: 'Please wait while we update the category.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/controls/updatecatController.php',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(data) {
            try {
                var response = JSON.parse(data);
                if (response.success) {
                    // Hide the modal and clean up
                    $('#editModal').modal('hide');
                    $('.modal-backdrop').remove();
                    $('#editModal').data('bs.modal', null);

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Category updated successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        showCategory();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to update category.'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid response from the server.',
                    footer: 'Please try again or contact support if the problem persists.'
                });
                console.error("Response:", data);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'There was an error updating the category.',
                footer: 'Please try again or contact support if the problem persists.'
            });
        }
    });
}

// Delete category function
function categoryDelete(categoryId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the category.',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/controls/deletecatController.php',
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
                            text: response.message || 'Failed to delete category.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while deleting the category: ' + error,
                        footer: 'Please try again or contact support if the problem persists.'
                    });
                }
            });
        }
    });
}

// Function to refresh the entire category list section dynamically
function refreshCategoryList() {
    updateContent("adminview/viewAllCategory.php", {}, '.allContent-section');
}


function showmessage() {
    console.log("showmessage function called");
    $.ajax({
        url: "adminview/message.php",
        method: "post",
        data: { record: 1 },
        success: function(data) {
            console.log("AJAX request successful");
            $('.allContent-section').html(data);
        },
        error: function() {
            console.error("Error loading messages.");
            alert("Error.");
        }
    });
}

