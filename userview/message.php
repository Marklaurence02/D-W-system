<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="chat-container">
        <div class="header">Chat with us</div>
        <div id="chat-box" class="message">
            <!-- Persistent welcome message content -->
            <div class="welcome-message">
                <div class="welcome-sent">Welcome ka Dine&Watch! I am your Dine&Watch virtual assistant and I'll be happy to answer your questions.For an uninterrupted conversation with us, please ensure that you have a stable internet connection.Please tell me what you would like to know:</div>
            </div>
        </div>
        <div class="options row gx-2 gy-2 p-3">
            <div class="col-12 option" data-question="No Refund Policy?">No Refund Policy?</div>
            <div class="col-12 option" data-question="What time Dine&Watch Open">What time Dine&Watch Open</div>
            <div class="col-12 option" data-question="FAQ">FAQ</div>
        </div>
        <div class="input-container d-flex p-2">
            <input type="text" id="message-input" class="form-control me-2" placeholder="Type something...">
            <button id="send-btn" class="btn btn-primary">Send</button>
        </div>
    </div>
</div>


<script>
let selectedUserId = null;
const chatBox = document.getElementById('chat-box');
const notificationBadge = document.getElementById('notification-badge');
const responses = {
    "No Refund Policy?": "Dine&Watch has a strict no refund policy. All sales are final.",
    "What time Dine&Watch Open": "Dine&Watch is open from 10 AM to 10 PM daily.",
    "FAQ": "Here are some common questions:\n- No Refund Policy\n- Opening Hours\n- Reservations"
};

// Display the initial welcome message
function displayWelcomeMessage() {
    chatBox.innerHTML = `
        <div class="welcome-message">
            <div>Welcome ka Dine&Watch! I am your Dine&Watch virtual assistant and I'll be happy to answer your questions.</div>
            <div>For an uninterrupted conversation with us, please ensure that you have a stable internet connection.</div>
            <div>Please tell me what you would like to know:</div>
        </div>`;
}

// Initialize chat and load welcome message, session, staff assignment, and messages
document.addEventListener('DOMContentLoaded', async function() {
    displayWelcomeMessage(); // Display initial welcome message on load
    await assignStaff();
    loadMessages();
    checkSession();
});

// Load messages and maintain welcome message at the top
function loadMessages() {
    if (selectedUserId) {
        $.ajax({
            url: "/messagecontrol/get_messages.php",
            method: "GET",
            data: { receiver: selectedUserId },
            success: function(data) {
                displayMessages(data);

                const unreadMessages = data.filter(msg => msg.receiver_id === currentUserId && !msg.read_status);
                if (unreadMessages.length > 0) {
                    notificationBadge.style.display = 'flex';
                    notificationBadge.textContent = unreadMessages.length;
                } else {
                    notificationBadge.style.display = 'none';
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching messages:", error);
            }
        });
    }
}

// Display loaded messages without overwriting welcome message
function displayMessages(messages) {
    chatBox.innerHTML = ''; // Clear all messages but will add welcome message back
    displayWelcomeMessage(); // Keep welcome message on top

    messages.forEach(msg => {
        const sender = msg.sender_id === currentUserId ? 'You' : msg.first_name;
        const messageHtml = `<div class="${msg.sender_id === currentUserId ? 'sent' : 'received'}">
                                <strong>${sender}:</strong> ${msg.message}
                             </div>`;
        chatBox.insertAdjacentHTML('beforeend', messageHtml);
    });
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Display single message in chat
function displayMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = type === 'sent' ? 'sent' : 'received';
    messageDiv.textContent = message;
    chatBox.appendChild(messageDiv);
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Check session periodically
function checkSession() {
    setInterval(async function() {
        const response = await fetch('/messagecontrol/get_current_user.php');
        if (response.status === 401) {
            alert("Your session has expired. Please log in again.");
            window.location.href = '/login.php';
        }
    }, 60000);
}

// Handle option clicks for predefined responses
document.querySelectorAll('.option').forEach(option => {
    option.addEventListener('click', function() {
        const question = this.getAttribute('data-question');
        document.getElementById('message-input').value = question;
        sendMessage(question);
    });
});

// Send button handler
document.getElementById('send-btn').addEventListener('click', function() {
    const message = document.getElementById('message-input').value.trim();
    if (message) {
        sendMessage(message);
    }
});

async function assignStaff() {
    try {
        const response = await fetch('/messagecontrol/AssignStaff.php');
        if (!response.ok) throw new Error(`Failed to assign staff: ${response.statusText}`);
        
        const data = await response.json();
        console.log("Response from assign staff:", data); // Debugging log
        
        if (data && typeof data.assigned_staff_id === 'number') {
            selectedUserId = data.assigned_staff_id;

            // If no human staff is available (assuming bot ID 0 means no staff)
            if (selectedUserId === 0) {
                alert("No human staff available; a virtual assistant will handle your messages.");
            } else {
                console.log(`Assigned staff ID: ${selectedUserId}`);
            }
        } else {
            alert("Staff assignment failed: No staff available at this time.");
            selectedUserId = 0; // Default to bot if no staff assigned
        }
    } catch (error) {
        console.error("Error assigning staff:", error);
        alert("An error occurred during staff assignment. Please try again later.");
        selectedUserId = 0; // Default to bot on error
    }
}

// Send message handler
async function sendMessage(message) {
    // Check if the message is a predefined response
    const response = responses[message];
    if (response) {
        displayMessage(message, 'sent'); // Show the sent message in the chat
        displayMessage(response, 'received'); // Show the bot's predefined response
    } else {
        displayMessage(message, 'sent'); // Otherwise, display the sent message
        document.getElementById('message-input').value = ''; // Clear input after sending the message

        if (selectedUserId) {
            // Fetch whether the selected user (e.g., Admin) has a staff member assigned
            const staffCheckResponse = await fetch(`/messagecontrol/check_staff_assignment.php?user_id=${selectedUserId}`);
            const staffCheckData = await staffCheckResponse.json();

            if (staffCheckData.status === 'no_staff') {
                // If no staff is assigned, show the message and prevent message sending
                alert("No staff assigned to handle the message.");
                return; // Stop the function from proceeding
            }

            // If there is staff assigned, proceed to send the message
            const response = await fetch('/messagecontrol/post_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ receiver: selectedUserId, message })
            });
            const data = await response.json();
            if (data.status === 'success') {
                loadMessages(); // Reload messages after successful send
            } else {
                alert(data.message || "Failed to send message.");
            }
        } else {
            alert("No staff assigned to handle the message.");
        }

        // Simulate an automatic reply (for chatbot responses, if any)
        setTimeout(() => displayMessage("Sorry, I don't have an answer for that.", 'received'), 500);
    }
}
</script>




<style>
.chat-container {
    width: 100%;
    max-width: 400px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.header {
    background-color: #005bb5;
    color: #ffffff;
    padding: 16px;
    text-align: center;
    font-weight: bold;
}

.message {
    padding: 16px;
    background-color: #e8f4ff; /* Slightly blue background for the message area */
    color: #333;
    font-size: 14px;
    max-height: 300px;
    overflow-y: auto;
}

.welcome-message {
    margin-bottom: 10px;
}

.options .option {
    background-color: #d1e7dd;
    color: #005bb5;
    padding: 10px;
    border-radius: 10px;
    text-align: center;
    font-size: 14px;
    cursor: pointer;
    margin-bottom: 5px;
    border: 1px solid #ddd;
}

.options .option:hover {
    background-color: #005bb5;
    color: #ffffff;
}

.input-container input {
    border: none;
    outline: none;
}

.notification-badge {
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 5px;
    display: none;
    font-size: 0.8rem;
    position: absolute;
    top: 10px;
    right: 10px;
}


.welcome-sent{
    background-color: #d1e7dd;
    padding: 8px;
    border-radius: 10px;
    margin: 5px 0;
    text-align: left;
    font-size: 14px;
    color: #005bb5;

}

.sent {
    background-color: #d1e7dd;
    padding: 8px;
    border-radius: 10px;
    margin: 5px 0;
    text-align: left;
    font-size: 14px;
    color: #005bb5;
}

.received {
    background-color: #f8d7da;
    padding: 8px;
    border-radius: 10px;
    margin: 5px 0;
    text-align: left;
    font-size: 14px;
    color: #b23b3b;
}
</style>
