<?php
include_once "./assets/config.php"; // Include the DB connection file
?>


<!-- Sidebar -->
<div class="md-3 d-flex flex-column flex-shrink-0 p-3 text-white sidebar" id="mySidebar">
    <!-- Profile Section -->
    <div class="profile-section">
        <!-- Mobile/Collapsed View -->
        <div class="mobile-profile">
            <img src="./images/admin.png" class="mobile-logo" alt="Logo" title="<?php echo isset($user['first_name']) ? htmlspecialchars($user['first_name']) : 'User'; ?>">
        </div>

        <!-- Desktop View -->
        <div class="desktop-profile">
            <div class="profile-container">
                <div class="profile-image-container">
                    <img src="./images/admin.png" class="profile-image" id="editProfileBtn" alt="Profile">
                </div>
                <div class="profile-info">
                    <?php
                    if (isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];
                        $sql = "SELECT first_name, role FROM users WHERE user_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('i', $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $user = $result->fetch_assoc();

                        if ($user) {
                            echo '<div class="user-name">' . htmlspecialchars($user['first_name']) . '</div>';
                            echo '<div class="user-role">' . htmlspecialchars($user['role']) . '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <hr class="sidebar-divider">
    
    <!-- Navigation Items -->
    <ul class="nav nav-pills flex-column">
        <li class="nav-item"><a href="Owner-panel.php" class="nav-link text-white"><i class="fa fa-chart-bar sideicon"></i><span class="ml-2">Dashboard</span></a></li>
        <li class="nav-item"><a href="#orders" onclick="showOrders()" class="nav-link text-white"><i class="fas fa-receipt sideicon"></i><span class="ml-2">Orders</span></a></li>
        <li class="nav-item"><a href="#reservation" onclick="showReservation()" class="nav-link text-white"><i class="fa fa-calendar-check-o sideicon"></i><span class="ml-2">Table Reservations</span></a></li>
        <li class="nav-item"><a href="#category" onclick="showCategory()" class="nav-link text-white"><i class="fa fa-line-chart sideicon"></i><span class="ml-2">Category</span></a></li>
        <li class="nav-item"><a href="#products" onclick="showProductItems()" class="nav-link text-white"><i class="fas fa-pizza-slice sideicon"></i><span class="ml-2">Products</span></a></li>
        <li class="nav-item"><a href="#tables" onclick="showTableViews()" class="nav-link text-white"><i class="fas fa-table sideicon"></i><span class="ml-2">Tables</span></a></li>
        <li class="nav-item"><a href="#users" onclick="showUser()" class="nav-link text-white"><i class="fa fa-users sideicon"></i><span class="ml-2">Users</span></a></li>
        <li class="nav-item"><a href="#admin" onclick="showadmin()" class="nav-link text-white"><i class="fa fa-user-plus sideicon"></i><span class="ml-2">Admin Management</span></a></li>
        <li class="nav-item"><a href="#activity-log" onclick="showActivity_log()" class="nav-link text-white"><i class="fas fa-history sideicon"></i><span class="ml-2">Activity Log</span></a></li>
        <li class="nav-item"><a href="message.php"class="nav-link text-white"><i class="fa fa-envelope sideicon"></i><span class="ml-2">Messages</span></a></li>
    </ul>
</div>

<!-- Update Profile Modal -->
<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateProfileModalLabel">
                    <i class="fa fa-user-edit me-2"></i>Update Profile
                </h5>
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
                                <input type="text" class="form-control" id="zip_code" name="zip_code" minlength="4" maxlength="5" pattern="[0-9]{4,5}" title="Zip code must be 4 or 5 digits">
                            </div>
                            <div class="input-group">
                                <label for="old_password" class="form-label w-100">Old Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="old_password" name="old_password">
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
                                    <input type="password" class="form-control" id="password" name="password"minlength="8" pattern=".{8,}" 
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
    fetch('assets/getprofiledata.php?user_id=' + userId)
        .then(response => response.json())
        .then(user => {
            // Reset button state
            this.disabled = false;
            this.innerHTML = '<i class="fa fa-pencil"></i>';

            if (user && !user.error) {
                // Populate modal fields
                document.getElementById('first_name').value = user.first_name || '';
                document.getElementById('middle_initial').value = user.middle_initial || '';
                document.getElementById('last_name').value = user.last_name || '';
                document.getElementById('contact_number').value = user.contact_number || '';
                document.getElementById('email').value = user.email || '';
                document.getElementById('address').value = user.address || '';
                document.getElementById('zip_code').value = user.zip_code || '';
                document.getElementById('username').value = user.username || '';

                // Show modal using jQuery
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
            fetch('assets/updateprofile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Success', data.message, 'success').then(() => {
                        // Hide the modal
                        $('#updateProfileModal').modal('hide');
                        // Redirect to Owner-panel.php
                        window.location.href = 'Owner-panel.php';
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






<style>
.sidebar {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    width: 250px;
    height: calc(100vh - 60px);
    position: fixed;
    top: 60px;
    left: 0;
    transition: all 0.3s ease;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    overflow-y: auto;
    z-index: 1020;
    
    /* Hide scrollbar */
    &::-webkit-scrollbar {
        display: none;
    }
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Collapsed state */
.sidebar.collapsed {
    width: 70px;
    
    .profile-section {
        padding: 0.8rem 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .desktop-profile {
        display: none !important;
    }
    
    .mobile-profile {
        display: flex !important;
        justify-content: center;
        align-items: center;
        width: 100%;
        
        .mobile-logo {
            width: 40px;
            height: 40px;
            margin: 0;
            display: block;
        }
    }
    
    .profile-info, 
    .edit-profile-btn {
        display: none;
    }
}

/* Navigation styles */
.nav-pills .nav-link {
    color: white;
    border-radius: 8px;
    margin: 0.2rem 0.5rem;
    padding: 0.8rem 1rem;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

/* Hover effect */
.nav-pills .nav-link:hover {
    background-color: rgba(255,255,255,0.1);
    transform: translateX(5px);
}

/* Active state */
.nav-pills .nav-link.active {
    background-color: rgba(255,255,255,0.2);
    border-left: 4px solid white;
}

/* Icon styles */
.nav-pills .nav-link i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
    transition: all 0.3s ease;
}

/* Mobile styles */
@media (max-width: 768px) {
    .sidebar {
        left: -250px;
    }
    
    .sidebar.show {
        left: 0;
    }
    
    .nav-pills .nav-link {
        margin: 0.2rem;
        padding: 0.8rem;
    }
}

.sidebar-header {
    padding: 1rem;
}

.sidebar-divider {
    border-color: rgba(255,255,255,0.2);
    margin: 1rem 0;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.8rem 1rem;
    margin: 0.2rem 0;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.nav-link i {
    width: 20px;
    margin-right: 10px;
    font-size: 1.1rem;
}

.nav-link span {
    font-size: 0.9rem;
}

.nav-link:hover {
    background-color: rgba(255,255,255,0.1);
    transform: translateX(5px);
}

.nav-link.active {
    background-color: rgba(255,255,255,0.2);
    border-left: 4px solid white;
}

.profile-pic {
    width: 80px;
    height: 80px;
    border: 3px solid rgba(255,255,255,0.3);
    padding: 2px;
    transition: all 0.3s ease;
}

.profile-pic:hover {
    transform: scale(1.05);
}



#editProfileBtn:hover {
    background-color: rgba(255,255,255,0.3);
    transform: rotate(15deg);
}

/* Collapsed state styles */
.sidebar.collapsed .nav-link span,
.sidebar.collapsed #welcomeText {
    display: none;
}

.sidebar.collapsed .profile-pic {
    width: 50px;
    height: 50px;
}

@media (max-width: 768px) {
    .sidebar {
        left: -250px;
        z-index: 1000;
    }
    
    .sidebar.show {
        left: 0;
    }
}

/* Enhanced Modal Styles */
.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.modal-header {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 1.5rem;
    border: none;
}

.modal-title {
    font-weight: 600;
    font-size: 1.25rem;
}

.modal-body {
    padding: 2rem;
}

.form-label {
    font-weight: 500;
    color: #555;
    margin-bottom: 0.5rem;
}

.form-control {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #FD6610;
    box-shadow: 0 0 0 0.2rem rgba(253, 102, 16, 0.15);
}

.btn-close {
    color: white;
    opacity: 1;
    transition: all 0.3s ease;
}

.btn-close:hover {
    opacity: 0.75;
}

.modal-footer {
    border-top: 1px solid #eee;
    padding: 1.5rem;
}

.btn-secondary {
    background-color: #6c757d;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #FD6610 0%, #FF8142 100%);
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-primary:hover, .btn-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Input group styling */
.input-group {
    margin-bottom: 1.5rem;
}

/* Responsive adjustments for the modal */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .modal-body {
        padding: 1rem;
    }
}

/* Profile Section Styles */
.profile-section {
    padding: 1.5rem 1rem;
    text-align: center;
}

/* Mobile Profile */
.mobile-profile {
    padding: 0.5rem;
}

.mobile-logo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.2);
}

/* Desktop Profile */
.profile-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.profile-image-container {
    position: relative;
    display: inline-block;
}

.profile-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,0.3);
    padding: 3px;
    background-color: white;
    transition: transform 0.3s ease;
}

.profile-image:hover {
    transform: scale(1.05);
}

.edit-profile-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.edit-profile-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(15deg);
}

.profile-info {
    text-align: center;
}

.user-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: white;
    margin-bottom: 0.25rem;
}

.user-role {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.8);
    font-weight: 500;
}

.sidebar-divider {
    margin: 1rem 0;
    border-color: rgba(255,255,255,0.2);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .profile-section {
        padding: 1rem 0.5rem;
    }

    .sidebar.collapsed .profile-section {
        padding: 0.5rem;
    }

    .sidebar.collapsed .desktop-profile {
        display: none !important;
    }
}

/* Sidebar Navigation Styles */
.nav-pills .nav-link {
    color: white;
    border-radius: 8px;
    margin: 0.2rem 0;
    padding: 0.8rem 1rem;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
    background-color: rgba(255,255,255,0.1);
    transform: translateX(5px);
}

.nav-pills .nav-link.active {
    background-color: rgba(255,255,255,0.2);
    border-left: 4px solid white;
}

.nav-pills .nav-link i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

/* Collapsed State */
.sidebar.collapsed {
    width: 70px;
}

.sidebar.collapsed .nav-link span {
    display: none;
}

.sidebar.collapsed .nav-link i {
    margin-right: 0;
    font-size: 1.2rem;
}

.sidebar.collapsed .nav-link {
    padding: 0.8rem;
    justify-content: center;
}

/* Profile Section Base Styles */
.profile-section {
    padding: 1.5rem 1rem;
    text-align: center;
    transition: all 0.3s ease;
}

/* Mobile Profile */
.mobile-profile {
    display: none; /* Hidden by default */
}

.mobile-logo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.2);
    transition: all 0.3s ease;
}

/* Desktop Profile */
.profile-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.profile-image-container {
    position: relative;
    display: inline-block;
    transition: all 0.3s ease;
}

.profile-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,0.3);
    padding: 3px;
    background-color: white;
    transition: all 0.3s ease;
}

/* Collapsed State Styles */
.sidebar.collapsed {
    width: 70px;
    
    .profile-section {
        padding: 0.8rem 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .desktop-profile {
        display: none !important;
    }
    
    .mobile-profile {
        display: flex !important;
        justify-content: center;
        align-items: center;
        width: 100%;
        
        .mobile-logo {
            width: 40px;
            height: 40px;
            margin: 0;
            display: block;
        }
    }
    
    .profile-info, 
    .edit-profile-btn {
        display: none;
    }
}

/* Mobile State Styles */
@media (max-width: 768px) {
    .sidebar {
        .profile-section {
            padding: 1rem;
        }
        
        .mobile-profile {
            display: block;
        }
        
        .desktop-profile {
            display: none;
        }
    }
}

/* Hover Effects */
.profile-image:hover {
    transform: scale(1.05);
    border-color: rgba(255,255,255,0.5);
}

.mobile-logo:hover {
    transform: scale(1.1);
    border-color: rgba(255,255,255,0.4);
}

.edit-profile-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    opacity: 0;
}

.profile-image-container:hover .edit-profile-btn {
    opacity: 1;
}

.edit-profile-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(15deg);
}

/* Profile Info Styles */
.profile-info {
    text-align: center;
    transition: all 0.3s ease;
}

.user-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: white;
    margin-bottom: 0.25rem;
    transition: all 0.3s ease;
}

.user-role {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.8);
    font-weight: 500;
    transition: all 0.3s ease;
}

/* Divider */
.sidebar-divider {
    margin: 0.5rem 0;
    border-color: rgba(255,255,255,0.2);
    transition: all 0.3s ease;
}

/* Update the HTML structure */
</style>