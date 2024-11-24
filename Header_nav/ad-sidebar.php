<?php
include_once "assets/config.php";
?>


<!-- Sidebar -->
<div class="md-3 d-flex flex-column flex-shrink-0 p-3 text-white" style="background-color: rgba(253, 102, 16, 0.8);" id="mySidebar">
<div class="text-center mb-4" id="welcomeMessage">
    <!-- Logo displayed when the sidebar is collapsed -->
    <img src="./images/admin.png" class="logo d-md-none" width="50" height="50" alt="Logo" title="Dine&Watch">

    <!-- Profile picture and welcome message displayed when expanded -->
    <div class="position-relative">
        <img src="./images/admin.png" class="rounded-circle profile-pic" width="80" height="80" alt="Dine&Watch">
        <!-- Pen Icon for Editing Profile -->
        <span id="editProfileBtn" class="position-absolute bottom-0 end-0 p-1" style="cursor: pointer;">
            <i class="fa fa-pencil text-white"></i>
        </span>
    </div>

    <h5 class="mt-3 d-none d-md-block" id="welcomeText">
        <?php
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT first_name FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                echo '<div>Welcome, <b>' . htmlspecialchars($user['first_name']) . '</b> (' . htmlspecialchars($_SESSION['role']) . ')</div>';
            } else {
                echo "<div>Welcome, User</div>";
            }
        } else {
            echo "<div>Welcome, Guest</div>";
        }
        ?>
    </h5>
</div>


    <hr class="bg-light">
    <a href="#" class="closebtn text-white d-md-none" onclick="toggleNav()">×</a>
    <ul class="nav nav-pills flex-column">
        <li class="nav-item"><a href="Admin-panel.php" class="nav-link text-white"><i class="fa fa-cart-arrow-down sideicon"></i><span class="ml-2">Dashboard</span></a></li>
        <li class="nav-item"><a href="#orders" onclick="showOrders()" class="nav-link text-white"><i class="fas fa-receipt sideicon"></i><span class="ml-2">Orders</span></a></li>
        <li class="nav-item"><a href="#reservation" onclick="showReservation()" class="nav-link text-white"><i class="fa fa-calendar-check-o sideicon"></i><span class="ml-2">Table Reservations</span></a></li>
        <li class="nav-item"><a href="#category" onclick="showCategory()" class="nav-link text-white"><i class="fa fa-line-chart sideicon"></i><span class="ml-2">Category</span></a></li>
        <li class="nav-item"><a href="#products" onclick="showProductItems()" class="nav-link text-white"><i class="fas fa-pizza-slice sideicon"></i><span class="ml-2">Products</span></a></li>
        <li class="nav-item"><a href="#tables" onclick="showTableViews()" class="nav-link text-white"><i class="fas fa-table sideicon"></i><span class="ml-2">Tables</span></a></li>
        <li class="nav-item"><a href="#users" onclick="showUser()" class="nav-link text-white"><i class="fa fa-users sideicon"></i><span class="ml-2">Users</span></a></li>
        <li class="nav-item"><a href="Amessage.php" class="nav-link text-white"><i class="fa fa-envelope sideicon"></i><span class="ml-2">Messages</span></a></li>
    </ul>
</div>

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
        url: 'assets/getprofiledata.php', // PHP file to fetch user data
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
    fetch('assets/updateprofile.php', {
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






<style>
/* Sidebar Styling */
#mySidebar {
    width:200px;
    transition: all 0.4s ease;
    height: 100%;
}

#mySidebar.collapsed {
    width: fit-content;
    height: 100%;
}

#mySidebar.collapsed .nav-link span {
    display: none;
}

#main {
    transition: margin-left 0.4s ease;
}

.nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
    border-left: 5px solid #FD6610;
    color: #fff;
    font-weight: bold;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transition: background-color 0.3s ease;
}

#mySidebar.collapsed .profile-pic {
    display: none !important;
}

#mySidebar.collapsed #welcomeText {
    display: none !important;
}

#mySidebar .logo {
    display: none;
}

#mySidebar.collapsed .logo {
    display: block !important;
    margin: auto;
}

.profile-pic {
    transition: all 0.3s ease-in-out;
}
</style>