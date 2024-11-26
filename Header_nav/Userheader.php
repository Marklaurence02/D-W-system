<?php
session_name("user_session");
session_start();

// Use session variables directly for displaying user info
$first_name = htmlspecialchars($_SESSION['first_name'] ?? 'User');
$role = htmlspecialchars($_SESSION['role'] ?? 'User');

?>

<header class="custom-header d-flex justify-content-between align-items-center p-3">
    <!-- Logo linking to the landing page -->
    <a>
    <img src="Images/dinewatchlogo.png" alt="Dine & Watch Logo" class="logo-img">
    </a>
    
    <!-- Menu icon to open the sidebar -->
    <button class="openbtn" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
</header>

<!-- Sidebar structure -->
<div id="mySidebar" class="sidebar">
    <div class="side-header text-center">
        <img src="Images/admin.png" width="100" height="100" alt="User" class="rounded-circle" id="editProfileBtn">
        <h5 class="mt-3">
            <div class="welc">Welcome, <?= $first_name; ?> (<?= $role; ?>)</div>
        </h5>
    </div>
    <hr style="border:1px solid; background-color:#8a7b6d; border-color:#3B3131;">
    <a href="javascript:void(0)" class="closebtn" onclick="toggleSidebar()">&times;</a>
    <a href="User-Panel.php"><i class="fa fa-home"></i> Home</a>
    <a href="#orders" onclick="order_list()"><i class="fa fa-cart-arrow-down"></i> Orders</a>
    <a href="#reservation" onclick="savedreservation()"><i class="fa fa-calendar-check-o"></i> Reservations</a>
    <a href="#reservation" onclick="recieptrecords()"><i class="fa fa-calendar-check-o"></i> Receipt</a>
    <a href="#reschedule" onclick="reschedule()"><i class="fa fa-calendar-check-o"></i> Reschedule</a>
    <a href="#reschedule" onclick="Feedback()"><i class="fa fa-calendar-check-o"></i>FeedBack</a>

    <a href="#Message"onclick="messageview()"><i class="fa fa-cog"></i> Message</a>
    <a href="assets/log-out.php"><i class="fa fa-sign-out"></i> Log-out</a>
</div>

<script>
function toggleSidebar() {
    console.log("Toggling sidebar");
    const sidebar = document.getElementById("mySidebar");
    const mainContent = document.getElementById("mainContent");
    console.log("Sidebar:", sidebar);
    console.log("Main content:", mainContent);

    if (sidebar) {
        sidebar.style.width = sidebar.style.width === "250px" ? "0" : "250px";
        if (mainContent) {
            mainContent.style.marginRight = sidebar.style.width === "250px" ? "250px" : "0";
        }
    } else {
        console.error("Sidebar element not found");
    }
}

</script>
<style>
/* Sidebar styling */
.sidebar {
    height: 100%;
    width: 0;
    position: fixed;
    top: 0;
    right: 0;
    background-color: #8B0000; /* Tomato red */
    overflow-x: hidden;
    transition: 0.3s;
    z-index: 1000;
    padding-top: 60px;
    box-shadow: -2px 0 5px rgba(0,0,0,0.5);
}

/* Sidebar links */
.sidebar a {
    padding: 15px 25px;
    text-decoration: none;
    font-size: 1.2rem;
    color: #FFD700; /* Cheese yellow */
    display: block;
    transition: 0.3s;
}

.sidebar a:hover {
    background-color: #556B2F; /* Basil green */
}

/* Sidebar header styling */
.side-header {
    padding: 20px;
    color: #FFD700; /* Cheese yellow */
    background-color: #8B0000; /* Tomato red */
}

.welc {
    font-size: 1.1rem;
    color: #FFD700; /* Cheese yellow */
}

/* Close button */
.closebtn {
    position: absolute;
    top: 10px;
    right: 25px;
    font-size: 2rem;
    color: #FFD700; /* Cheese yellow */
    background: none;
    border: none;
    cursor: pointer;
}

/* Open button styling */
.openbtn {
    font-size: 1.5rem;
    color: #8B0000; /* Tomato red */
    background: none;
    border: none;
    cursor: pointer;
}

#main .openbtn {
    position: fixed;
    top: 10px;
    right: 10px;
}

/* Modal styling */
.modal-content {
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.modal-header {
    background-color: #8B0000; /* Tomato red */
    color: #FFD700; /* Cheese yellow */
    border-bottom: 1px solid #556B2F; /* Basil green */
}

.modal-title {
    font-size: 1.25rem;
}

.modal-body {
    padding: 20px;
}

.btn-primary {
    background-color: #FFD700; /* Cheese yellow */
    border-color: #FFD700;
}

.btn-primary:hover {
    background-color: #556B2F; /* Basil green */
    border-color: #556B2F;
}
</style>
<!-- Update Profile Modal -->
<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateProfileModalLabel">Update Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateProfileForm" onsubmit="event.preventDefault(); updateProfile();">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="zip_code" class="form-label">Zip Code</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code">
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="middle_initial" class="form-label">Middle Initial</label>
                                <input type="text" class="form-control" id="middle_initial" name="middle_initial">
                            </div>
                            <div class="mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address">
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
// Show modal and pre-fill form fields
document.getElementById('editProfileBtn').addEventListener('click', function () {
    // Fetch current user data from the server
    const userId = <?php echo $_SESSION['user_id']; ?>; // Assuming session has user_id

    // AJAX request to get the current profile information
    $.ajax({
        url: 'assets/datauser.php', // PHP file to fetch user data
        method: 'GET',
        data: { user_id: userId },
        success: function (response) {
            console.log('Profile Data:', response); // Debugging log to check the response

            // Parse the JSON response and check if data exists
            try {
                const user = JSON.parse(response);
                if (user && !user.error) {  // Ensure there is no error in the response
                    // Populate the form fields with the fetched data
                    document.getElementById('first_name').value = user.first_name;
                    document.getElementById('middle_initial').value = user.middle_initial;
                    document.getElementById('last_name').value = user.last_name;
                    document.getElementById('contact_number').value = user.contact_number;
                    document.getElementById('email').value = user.email;
                    document.getElementById('address').value = user.address;
                    document.getElementById('zip_code').value = user.zip_code;

                    // Ensure username field is correctly populated (no hardcoded "root" value)
                    if (user.username) {
                        document.getElementById('username').value = user.username;
                    } else {
                        // In case username is not found in response, you can show an error or handle it
                        alert("Error: Username not found in response.");
                    }

                    // Show the modal
                    $('#updateProfileModal').modal('show');
                } else {
                    alert('Error: No user data found or ' + user.error);
                }
            } catch (e) {
                console.error('Error parsing JSON response:', e);
                alert('Error fetching profile data');
            }
        },
        error: function () {
            alert('Error fetching profile data');
        }
    });
});

// Update Profile Function
function updateProfile() {
    // Show loading alert
    alert("Loading... Please wait.");

    // Get the form element
    const form = document.getElementById('updateProfileForm');

    // Create a FormData object from the form
    const formData = new FormData(form);

    // Use the Fetch API to send the form data to the server
    fetch('assets/updateuser.php', {
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
            // Update the alert to show success
            alert("Profile updated successfully!");
            // Optionally refresh the UI or close the modal
            $("#updateProfileModal").modal("hide");
        } else {
            // Show error in alert
            alert('Error updating profile: ' + (data.message || 'Unknown error.'));
        }
    })
    .catch(error => {
        // Log and display any errors that occurred
        console.error('Error during update operation:', error);
        alert('An error occurred while updating the profile. Please check your network or try again.');
    });
}
</script>