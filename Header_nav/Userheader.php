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
    <a href="#orders" onclick="order_list()"><i class="fa fa-shopping-cart"></i> Orders</a>
    <a href="#reservation" onclick="savedreservation()"><i class="fa fa-calendar"></i> Reservations</a>
    <a href="#reservation" onclick="recieptrecords()"><i class="fa fa-file-text"></i> Receipt</a>
    <a href="#reschedule" onclick="reschedule()"><i class="fa fa-clock-o"></i> Reschedule</a>
    <a href="#feedback" onclick="Feedback()"><i class="fa fa-comment"></i> Feedback</a>
    <a href="#Message" onclick="messageview()"><i class="fa fa-envelope"></i> Message</a>
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
    background-color: #8B0000; 
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateProfileModalLabel">
                    <i class="fa fa-user-edit me-2"></i>Update Profile
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
                        </div>
            <div class="modal-body">
                <form id="updateProfileForm" onsubmit="event.preventDefault(); updateProfile();">
                    <div class="row g-3">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="first_name" class="form-label w-100">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="input-group">
                                <label for="last_name" class="form-label w-100">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="input-group">
                                <label for="email" class="form-label w-100">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="input-group">
                                <label for="zip_code" class="form-label w-100">Zip Code</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code">
                            </div>
                            <div class="input-group">
                                <label for="old_password" class="form-label w-100">Current Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="old_password" name="old_password" >
                                    <span class="input-group-text" id="toggleOldPassword" style="cursor: pointer;">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="middle_initial" class="form-label w-100">Middle Initial</label>
                                <input type="text" class="form-control" id="middle_initial" name="middle_initial">
                            </div>
                            <div class="input-group">
                                <label for="contact_number" class="form-label w-100">Contact Number</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number">
                            </div>
                            <div class="input-group">
                                <label for="address" class="form-label w-100">Address</label>
                                <input type="text" class="form-control" id="address" name="address">
                            </div>
                            <div class="input-group">
                                <label for="username" class="form-label w-100">Username</label>
                                <input type="text" class="form-control" id="username" name="username">
                            </div>

                            <div class="input-group">
                                <label for="password" class="form-label w-100">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           minlength="8" pattern=".{8,}" 
                                           title="Password must be at least 8 characters long">
                                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('editProfileBtn').addEventListener('click', function() {
    const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
    
    if (!userId) {
        Swal.fire('Please log in to update your profile');
        return;
    }

    // Show loading state
    this.disabled = true;
    this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

    // Fetch profile data
    fetch('assets/datauser.php?user_id=' + userId)
        .then(response => response.json())
        .then(user => {
            // Reset button state
            this.disabled = false;
            this.innerHTML = '<i class="fa fa-pencil"></i>';

            if (user && !user.error) {
                // Populate modal fields (removing old_password)
                document.getElementById('first_name').value = user.first_name || '';
                document.getElementById('middle_initial').value = user.middle_initial || '';
                document.getElementById('last_name').value = user.last_name || '';
                document.getElementById('contact_number').value = user.contact_number || '';
                document.getElementById('email').value = user.email || '';
                document.getElementById('address').value = user.address || '';
                document.getElementById('zip_code').value = user.zip_code || '';
                document.getElementById('username').value = user.username || '';
                // Remove password field population
                document.getElementById('old_password').value = ''; // Clear old password
                document.getElementById('password').value = ''; // Clear new password field

                $('#updateProfileModal').modal('show');
            } else {
                Swal.fire('Error', user.error || 'Failed to fetch profile data', 'error');
            }
        })
        .catch(error => {
            // Reset button state
            this.disabled = false;
            this.innerHTML = '<i class="fa fa-pencil"></i>';
            
            console.error('Error:', error);
            Swal.fire('Failed to fetch profile data', '', 'error');
        });
});

function updateProfile() {
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to save the changes?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, save it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading indicator
            Swal.fire({
                title: 'Saving...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Collect form data
            const formData = new FormData(document.getElementById('updateProfileForm'));

            // Send data to the server
            fetch('assets/updateuser.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Success', data.message, 'success').then(() => {
                        // Hide the modal
                        $('#updateProfileModal').modal('hide');
                        // Force reload the page to update the displayed information
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Failed to update profile', '', 'error');
            });
        }
    });
}

document.getElementById('toggleOldPassword').addEventListener('click', function() {
    const oldPasswordField = document.getElementById('old_password');
    const type = oldPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
    oldPasswordField.setAttribute('type', type);
    this.querySelector('i').classList.toggle('fa-eye-slash');
});

document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordField = document.getElementById('password');
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    this.querySelector('i').classList.toggle('fa-eye-slash');
});

</script>
