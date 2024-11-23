<?php
include '../assets/config.php';
header('Content-Type: text/html; charset=UTF-8');
session_start();
$users = [];
if (!isset($_SESSION['role'], $_SESSION['user_id'])) {
    error_log("User not authenticated or missing role");
    exit("<div class='alert alert-danger text-center'>User not authenticated.</div>");
}

$currentRole = $_SESSION['role'];
$currentUserId = $_SESSION['user_id'];
$rolesToFetch = [];

switch ($currentRole) {
    case 'Admin':
        $rolesToFetch = ['Owner', 'Staff'];
        break;
    case 'Owner':
        $rolesToFetch = ['Admin'];
        break;
    case 'Staff':
        $rolesToFetch = ['Admin', 'General User'];
        break;
    default:
        error_log("Undefined role: $currentRole");
        exit("<div class='alert alert-danger text-center'>Undefined user role.</div>");
}

// Construct SQL to get assigned users if the user is a Staff member
if ($currentRole === 'Staff') {
    $sql = "SELECT DISTINCT u.user_id, CONCAT(u.first_name, ' ', u.last_name) AS username, u.role
            FROM users u
            LEFT JOIN user_staff_assignments usa ON u.user_id = usa.user_id
            WHERE (u.role = 'Admin' OR (u.role = 'General User' AND usa.assigned_staff_id = ?))";
    $params = [$currentUserId];
    $paramTypes = 'i';
} else {
    // If the user is not Staff, use a simple role-based query
    if (!empty($rolesToFetch)) {
        $rolePlaceholders = implode(',', array_fill(0, count($rolesToFetch), '?'));
        $sql = "SELECT DISTINCT user_id, CONCAT(first_name, ' ', last_name) AS username, role
                FROM users
                WHERE role IN ($rolePlaceholders)";
        $params = $rolesToFetch;
        $paramTypes = str_repeat('s', count($rolesToFetch));
    }
}

try {
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception($conn->error);
    
    // Bind parameters based on the current user's role
    $stmt->bind_param($paramTypes, ...$params);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    exit("<div class='alert alert-danger text-center'>Error loading users.</div>");
}

$conn->close();
?>


    <div class="container mt-4">
        <!-- User List -->
        <div class="card mx-auto user-list" id="user-list">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Messages</h5>
                <div class="input-group">
                    <input type="text" id="search-input" class="form-control search-input d-none" placeholder="Search..." oninput="searchMessage()">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" onclick="toggleSearchInput()"><i class='bx bx-search-alt-2'></i></button>
                    </div>
                </div>
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
                                <div class="user-details">
                                    <div class="username">
                                        <?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>
                                        <span class="role">(<?php echo htmlspecialchars($user['role'], ENT_QUOTES); ?>)</span>
                                    </div>
                                    <small class="text-muted recent-message">Loading...</small>
                                    <div class="message-date text-muted"></div>
                                </div>
                            </div>
                            <span class="blue-dot d-none"></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="list-group-item text-center">No users available.</div>
                <?php endif; ?>
            </div>
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
            max-width: 100%;
            padding-top: 40px;
            align-items: center;
        }

        /* Card */
        .card {
            background-color: #f8f9fa;
            border: none;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
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
  /* Centered Date Separator */
        .separator-centered {
            text-align: center;
            margin: 10px 0;
            position: relative;
            font-size: 0.85rem;
            color: #888;
        }
        /* Conversation List */
        .list-group-item {
            background-color: #ffffff;
            color: #333333;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            border-left: 5px solid transparent;
            cursor: pointer;
            transition: background-color 0.2s, border-color 0.2s;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .list-group-item.border-blue {
            border-left-color: #007bff;
        }

        .list-group-item:hover {
            background-color: #f1f1f1;
        }

        /* Blue Dot for Unread Messages */
        .blue-dot {
            width: 10px;
            height: 10px;
            background-color: #007bff;
            border-radius: 50%;
            margin-left: 10px;
            display: inline-block;
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

        .username {
            font-weight: bold;
            font-size: 1rem;
        }

        .recent-message {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 2px;
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

        /* Hide the search input by default */
        .search-input {
            display: none;
        }
    </style>