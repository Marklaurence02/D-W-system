<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include '../assets/config.php';

header('Content-Type: text/html; charset=UTF-8');

$users = [];

// Check if the user is authenticated and has a role
if (!isset($_SESSION['role'], $_SESSION['user_id'])) {
    error_log("User not authenticated or missing role");
    exit("<div class='list-group-item text-center'>User not authenticated.</div>");
}

$currentRole = $_SESSION['role'];
$rolesToFetch = [];

// Define roles to fetch based on current user's role
switch ($currentRole) {
    case 'Admin':
        $rolesToFetch = ['Owner', 'Staff'];
        break;
    case 'Owner':
        $rolesToFetch = ['Admin', 'Staff'];
        break;
    case 'Staff':
        $rolesToFetch = ['Admin'];
        break;
    default:
        error_log("Undefined role: $currentRole");
        exit("<div class='list-group-item text-center'>Undefined user role.</div>");
}

if (!empty($rolesToFetch)) {
    // Prepare SQL query to fetch users based on roles
    $rolePlaceholders = implode(',', array_fill(0, count($rolesToFetch), '?'));
    $sql = "SELECT DISTINCT user_id, CONCAT(first_name, ' ', last_name) AS username, role 
            FROM users 
            WHERE role IN ($rolePlaceholders)";
    
    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception($conn->error);

        $stmt->bind_param(str_repeat('s', count($rolesToFetch)), ...$rolesToFetch);
        $stmt->execute();
        $result = $stmt->get_result();

        // Store all users in the $users array
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        exit("<div class='list-group-item text-center'>Error loading users.</div>");
    }
}

$conn->close();
?>

<div class="container mt-5">
    <!-- User List -->
    <div class="card mx-auto user-list" id="user-list">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Messages</h5>
        </div>
        <div class="list-group list-group-flush" id="user-list-content">
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center user-item" 
                         data-user-id="<?php echo $user['user_id']; ?>" 
                         onclick="openConversation(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')">
                        <div class="d-flex align-items-center">
                            <div class="user-initial">
                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                            </div>
                            <div>
                                <div class="username"><?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($user['role'], ENT_QUOTES); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item text-center">No users available.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Conversation View -->
    <div class="card mx-auto conversation-view d-none" id="conversation-view">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0" id="conversation-username">Username</h5>
            <button class="btn btn-outline-primary btn-sm" onclick="backToUserList()">Back</button>
        </div>
        <div class="card-body message-history" id="message-box">
            <!-- Messages will be dynamically loaded here -->
        </div>
        <div class="card-footer">
            <form id="messages-form" class="d-flex" onsubmit="sendMessage(event)">
                <input type="text" id="message-input" class="form-control" placeholder="Type a message..." required autocomplete="off">
                <button type="submit" class="btn btn-primary ml-2">Send</button>
            </form>
        </div>
    </div>
</div>





<style>
/* Container and Card */
.container {
    max-width: 600px;
    padding-top: 40px;
}

/* Card */
.card {
    background-color: #f8f9fa;
    border: none;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    max-width: 100%;
}

/* Header */
.card-header {
    background-color: #3B3131;
    color: #ffffff;
    padding: 20px 25px;
}

/* Button Styling */
.btn-outline-primary {
    color: #3B3131;
    border-color: #3B3131;
}

.btn-outline-primary:hover {
    background-color: #3B3131;
    color: #ffffff;
}

/* Search Bar */
.input-group .search-input {
    background-color: #ffffff;
    border: 1px solid #cccccc;
    color: #333333;
    padding: 12px;
    font-size: 1rem;
}

.input-group-text {
    background-color: #ffffff;
    border: 1px solid #cccccc;
    color: #333333;
}

/* Conversation List */
.list-group-item {
    background-color: #ffffff;
    color: #333333;
    padding: 20px 25px;
    border-bottom: 1px solid #e0e0e0;
    cursor: pointer;
    transition: background-color 0.2s;
    font-size: 1rem;
}

.list-group-item:hover {
    background-color: #f1f1f1;
}

/* User Initial */
.user-initial {
    width: 45px;
    height: 45px;
    background-color: #cccccc;
    color: #333333;
    font-size: 1.2rem;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin-right: 15px;
}

.message-history {
    padding: 15px;
    max-height: 400px;
    overflow-y: auto;
}

.message {
    margin-bottom: 15px;
    max-width: 100%;
}

.sent p {
    background-color: #007bff;
    color: #fff;
    padding: 10px 15px;
    border-radius: 12px;
    text-align: right;
    margin-left: auto;
    width: fit-content;
}

.received p {
    background-color: #e0e0e0;
    color: #333;
    padding: 10px 15px;
    border-radius: 12px;
    width: fit-content;
}


/* Hidden by default */
.d-none {
    display: none;
}

#message-box {
    max-height: 400px;
    overflow-y: auto;
}

</style>
