<?php
// updatemanagement.php

include_once "assets/config.php";  // Include your DB connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $role = $_POST['role']; // Admin or Staff

    $sql = "UPDATE users SET username = ?, email = ?, contact_number = ?, role = ?, updated_at = NOW() WHERE user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $username, $email, $contact_number, $role, $user_id);

    if ($stmt->execute()) {
        echo "User updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<form id="updateUserForm" method="POST" action="Ownerview/updatemanagement.php">
  <h2>Update Admin or Staff</h2>
  <input type="hidden" id="user_id" name="user_id" value="">

  <div class="form-group">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" class="form-control" required>
  </div>
  <div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" class="form-control" required>
  </div>
  <div class="form-group">
    <label for="contact_number">Contact Number</label>
    <input type="text" id="contact_number" name="contact_number" class="form-control" required>
  </div>
  <div class="form-group">
    <label for="role">Role</label>
    <select id="role" name="role" class="form-control">
      <option value="Admin">Admin</option>
      <option value="Staff">Staff</option>
    </select>
  </div>
  <button type="submit" class="btn btn-primary">Update User</button>
</form>
