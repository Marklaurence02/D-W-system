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


<script src="Js/Oviewmessage.js"></script>
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

        /* Add these new styles while keeping existing ones */
        .custom-select {
            background-color: #fff;
            border: 2px solid #3B3131;
            border-radius: 6px;
            padding: 8px 12px;
            font-weight: 500;
            min-width: 150px;
        }

        .custom-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(59, 49, 49, 0.25);
        }

        .card-header {
            background: linear-gradient(135deg, #3B3131 0%, #4a3f3f 100%);
        }

        .btn-primary {
            background: #3B3131;
            border: none;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #4a3f3f;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Enhanced user list items */
        .list-group-item {
            transition: all 0.3s ease;
            border-left: 5px solid #3B3131;
        }

        .list-group-item:hover {
            transform: translateX(5px);
            background-color: #f8f9fa;
        }

        .user-initial {
            background: linear-gradient(135deg, #3B3131 0%, #4a3f3f 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>