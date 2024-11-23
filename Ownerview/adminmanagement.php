<div class="admin-table">
  <h2>Admin and Staff Management</h2>

  <!-- Add Admin/Staff Button at the Top -->
  <div class="d-flex justify-content-between mb-3">
    <!-- Add Admin/Staff Button -->
    <button type="button" class="btn btn-secondary" style="height:40px" data-toggle="modal" data-target="#adminModal">
      Add Admin/Staff
    </button>
  </div>

  <!-- Admin/Staff List -->
  <div class="user-list">
  <table id="userTable" class="table table-striped">
    <thead class="thead">
        <tr>
            <th class="text-center">S.N.</th>
            <th class="text-center">Full Name</th>
            <th class="text-center">Username</th>
            <th class="text-center">Role</th>
            <th class="text-center">Contact Number</th>
            <th class="text-center">Email</th>
            <th class="text-center" colspan="2">Action</th>
        </tr>
    </thead>
    <tbody id="user_table_body">
        <?php
        include_once "../assets/config.php"; // Ensure this path is correct

        // Fetch all Admin/Staff members
        $sql = "SELECT * FROM users WHERE role IN ('Admin', 'Staff')";
        $result = $conn->query($sql);
        $count = 1;

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
        <tr class="user-row">
            <td class="text-center"><?= $count ?></td>
            <td class="text-center"><?= htmlspecialchars($row["first_name"] . " " . $row["last_name"]) ?></td>
            <td class="text-center"><?= htmlspecialchars($row["username"]) ?></td>
            <td class="text-center"><?= htmlspecialchars($row["role"]) ?></td>
            <td class="text-center"><?= htmlspecialchars($row["contact_number"]) ?></td>
            <td class="text-center"><?= htmlspecialchars($row["email"]) ?></td>
            <td class="text-center">
                <button class="btn btn-primary" style="height:40px" onclick="adminEditForm('<?= $row['user_id'] ?>')">Edit</button>
            </td>
            <td class="text-center">
                <button class="btn btn-danger" style="height:40px" onclick="adminDelete('<?= $row['user_id'] ?>')">Delete</button>
            </td>
        </tr>
        <?php
            $count++;
            }
        } else {
            echo "<tr><td colspan='8' class='text-center'>No Admin or Staff found</td></tr>";
        }
        ?>
    </tbody>
</table>



  </div>

<!-- Modal for Adding Admin/Staff -->
<div class="modal fade" id="adminModal" role="dialog" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="adminModalLabel">Add Admin/Staff</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="adminForm" onsubmit="addAdmin(); return false;">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required autocomplete="off">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="role">Role</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="Admin">Admin</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" required
                               pattern="09[0-9]{9}" title="Contact number must start with 09 and have exactly 11 digits.">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block my-3" style="height: 50px;">Add Admin/Staff</button>
                </form>
            </div>

        </div>
    </div>
</div>


<!-- Modal for Editing Admin/Staff -->
<div class="modal fade" id="editAdminModal" role="dialog" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editAdminModalLabel">Edit Admin/Staff</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editAdminFormContainer">
                <!-- The edit form will be loaded here via AJAX -->
            </div>

        </div>
    </div>
</div>
</div>


</div>
<script>
$(document).ready(function() {
    // Initialize DataTable with additional options for better functionality
    $('#userTable').DataTable({
        paging: true,                  // Enable pagination
        searching: true,               // Enable searching
        ordering: true,                // Enable sorting
        responsive: true,              // Make the table responsive
        autoWidth: false,              // Disable automatic column width adjustments for better layout
        columnDefs: [
            { orderable: false, targets: [6, 7] } // Disable ordering for Action columns
        ],
        pageLength: 10,                // Set the default number of rows per page
        lengthMenu: [5, 10, 25, 50],   // Options for rows per page dropdown
        language: {
            search: "_INPUT_",         // Customize the search input placeholder
            searchPlaceholder: "Search admins or staff...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries available",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "<<",
                last: ">>",
                next: ">",
                previous: "<"
            }
        }
    });
});
</script>


