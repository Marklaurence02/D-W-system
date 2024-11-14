<?php
session_name("owner_session");
include_once "../assets/config.php";  // Include your DB connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to perform this action.']);
    exit;
}

$logged_in_user_id = $_SESSION['user_id'];  // The currently logged-in user
$role = $_SESSION['role'];  // The role of the logged-in user
$username = $_SESSION['username'] ?? 'User';  // The username of the logged-in user

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'], $_POST['user_password'])) {
    $user_id = intval($_POST['user_id']);
    $user_password = $_POST['user_password'];

    // Retrieve the password hash for the logged-in user
    $password_sql = "SELECT password_hash FROM users WHERE user_id = ?";
    $password_stmt = $conn->prepare($password_sql);
    if ($password_stmt === false) {
        error_log("MySQL prepare error: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        exit;
    }

    $password_stmt->bind_param("i", $logged_in_user_id);
    $password_stmt->execute();
    $password_stmt->bind_result($hashed_password);

    if (!$password_stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Logged-in user not found.']);
        $password_stmt->close();
        exit;
    }
    $password_stmt->close();

    // Verify the provided password
    if (!password_verify($user_password, $hashed_password)) {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect password.']);
        exit;
    }

    // Fetch the details of the target user
    $user_sql = "SELECT * FROM users WHERE user_id = ?";
    $user_stmt = $conn->prepare($user_sql);
    if ($user_stmt === false) {
        error_log("MySQL prepare error: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        exit;
    }
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $result = $user_stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        $user_stmt->close();
        exit;
    }
    $user_stmt->close();

    // Generate the edit form HTML as a string
    ob_start();
    ?>
    <div>
        <h2>Edit Admin/Staff</h2>
        <p>Editing as: <strong><?= htmlspecialchars($username); ?></strong> (Role: <?= htmlspecialchars($role); ?>)</p>

        <form id="editAdminForm" onsubmit="updateadmin(<?= $user['user_id']; ?>); return false;">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="middle_initial">Middle Initial:</label>
                <input type="text" class="form-control" id="middle_initial" name="middle_initial" value="<?= htmlspecialchars($user['middle_initial']); ?>">
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']); ?>" required>
            </div>


            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="Admin" <?= $user['role'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="Staff" <?= $user['role'] == 'Staff' ? 'selected' : ''; ?>>Staff</option>
                </select>
            </div>

            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?= htmlspecialchars($user['contact_number']); ?>" required pattern="09[0-9]{9}" title="Contact number must start with 09 and have exactly 11 digits.">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['address']); ?>">
            </div>



            <button type="submit" class="btn btn-primary">Update Admin/Staff</button>
        </form>
    </div>
    <?php
    $form_html = ob_get_clean();
    echo json_encode(['status' => 'success', 'form_html' => $form_html]);
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
