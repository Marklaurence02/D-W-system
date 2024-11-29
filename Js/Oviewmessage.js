const MessageBox = document.getElementById('message-box');
let latestMessageTimestamp = null;  // Track the latest message timestamp
let selectedUserId = null;  // Track the selected user ID
let lastReceivedMessage = null; // Track the last received message
let lastReceivedMessageSenderId = null; // Track the sender ID of the last received message

document.addEventListener('DOMContentLoaded', () => {
    loadUserRecentMessages();
    setInterval(loadUserRecentMessages, 5000); // Auto-refresh recent messages every 5 seconds
    setInterval(loadMessages, 1000); // Auto-refresh messages in the conversation every 5 seconds
});

function loadUserRecentMessages() {
    const userItems = document.querySelectorAll('.user-item');

    userItems.forEach(userItem => {
        const userId = userItem.getAttribute('data-user-id');
        const recentMessageElement = userItem.querySelector('.recent-message');
        const blueDotElement = userItem.querySelector('.blue-dot');
        const messageDateElement = userItem.querySelector('.message-date'); // Element for the date
        const usernameElement = userItem.querySelector('.username'); // Element for the username

        // Fetch the most recent message and unread count for each user
        fetch(`/SmessageC/get_recent_message.php?user_id=${userId}`)
            .then(response => {
                if (!response.ok) throw new Error(`Network response was not ok: ${response.statusText}`);
                return response.json();
            })
            .then(data => {
                // Check if the data is available and display the most recent message
                if (data && data.recent_message) {
                    recentMessageElement.textContent = data.recent_message; // Update the recent message text

                    // Format the timestamp (removing seconds)
                    const messageDate = new Date(data.timestamp);
                    const formattedDate = formatMessageDate(messageDate);

                    // Display the formatted date
                    messageDateElement.textContent = formattedDate;

                    // Update the username with the role in the format username (role)
                    if (data.name && data.role) {
                        usernameElement.innerHTML = `${data.name} <span class="role">(${data.role})</span>`;
                    }

                    // Check if there are unread messages and display the blue dot
                    if (data.unread_count > 0) {
                        blueDotElement.classList.remove('d-none');
                        userItem.classList.add('border-blue');  // Add blue border for unread messages
                    } else {
                        blueDotElement.classList.add('d-none');
                        userItem.classList.remove('border-blue');
                    }
                } else {
                    // Fallback when no message data is available
                    recentMessageElement.textContent = "No messages yet";
                    messageDateElement.textContent = ""; // Clear the date if no message
                    blueDotElement.classList.add('d-none');
                    userItem.classList.remove('border-blue');
                }
            })
            .catch(error => {
                console.error("Error loading recent message :", error);
                recentMessageElement.textContent = "No messages yet";
                messageDateElement.textContent = ""; // Clear the date if error
                blueDotElement.classList.add('d-none');
                userItem.classList.remove('border-blue');
            });
    });
}

// Function to format the timestamp without seconds
function formatMessageDate(messageDate) {
    const options = {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true, // Use 12-hour clock with AM/PM
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    };

    // Return the formatted date without seconds
    return messageDate.toLocaleString('en-US', options); // You can adjust 'en-US' as needed
}


// Open a conversation with the selected user
function openConversation(userId, username) {
    selectedUserId = userId;  // Corrected variable name
    latestMessageTimestamp = null;
    document.getElementById('conversation-username').textContent = username;

    // Hide the user list and show the conversation view
    document.getElementById('user-list').classList.add('d-none');
    document.getElementById('conversation-view').classList.remove('d-none');

    // Clear unread indicator
    const userItem = document.querySelector(`.user-item[data-user-id="${userId}"]`);
    const blueDotElement = userItem.querySelector('.blue-dot');
    blueDotElement.classList.add('d-none');  // Hide blue dot
    userItem.classList.remove('border-blue');  // Remove blue border

    // Clear the message box before loading new messages
    MessageBox.innerHTML = ''; // Clear previous messages

    loadMessages();
}

// Load the messages of the selected conversation
// Load the messages of the selected conversation
function loadMessages() {
    if (!selectedUserId) {
        console.error("No user selected.");
        return;
    }

    const isAtBottom = MessageBox.scrollTop >= (MessageBox.scrollHeight - MessageBox.clientHeight - 20);
    const url = `/SmessageC/get_messages.php?receiver=${selectedUserId}` +
                (latestMessageTimestamp ? `&after=${latestMessageTimestamp}` : '');

    let lastMessageMinute = null;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.statusText}`);
            }
            return response.json();
        })
        .then(messages => {
            if (!messages.length) {
                // Display "No messages yet" message
                MessageBox.innerHTML = `
                    <div id="no-messages" class="text-center text-muted py-4">
                        <i class='bx bx-message-square-detail' style="font-size: 2rem;"></i>
                        <p class="mt-2">No messages yet. Start a conversation!</p>
                    </div>
                `;
                return;  // Exit if there are no messages
            }

            // Clear the "No messages yet" message if there are messages
            MessageBox.innerHTML = '';

            messages.forEach(msg => {
                const msgDate = new Date(msg.timestamp);
                const msgTimeString = msgDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const currentMessageMinute = msgDate.getHours() * 60 + msgDate.getMinutes();

                if (lastMessageMinute !== currentMessageMinute) {
                    const separatorElement = document.createElement('div');
                    separatorElement.classList.add('separator', 'separator-centered');
                    separatorElement.innerHTML = `<small class="timestamp">${msgTimeString}</small>`;
                    MessageBox.appendChild(separatorElement);
                }

                const messageElement = document.createElement('div');
                messageElement.classList.add('message', msg.sender_id === selectedUserId ? 'received' : 'sent');
                messageElement.innerHTML = `
                    <p><strong>${msg.first_name}:</strong> ${msg.message}</p>
                `;
                MessageBox.appendChild(messageElement);

                lastMessageMinute = currentMessageMinute;
            });

            if (messages.length > 0) {
                const latestMessage = messages[messages.length - 1].message;
                const latestMessageSenderId = messages[messages.length - 1].sender_id;
                latestMessageTimestamp = messages[messages.length - 1].timestamp;

                // Check if the latest message is different and from a different sender
                if (latestMessage !== lastReceivedMessage || latestMessageSenderId !== lastReceivedMessageSenderId) {
                    lastReceivedMessage = latestMessage;
                    lastReceivedMessageSenderId = latestMessageSenderId;

                    // Update the recent message and show the blue dot if the conversation is not open
                    updateRecentMessageForUser(selectedUserId, latestMessage, false);
                }
            }

            if (isAtBottom) MessageBox.scrollTop = MessageBox.scrollHeight;
        })
        .catch(error => console.error("Error loading messages:", error));
}



// Send a message to the selected user
let lastSentMessageMinute = null;  // Track the last message minute for sent messages

// Send a message to the selected user
function sendMessage(event) {
    event.preventDefault();  // Prevent form submission if it's a button click

    const messageInput = document.getElementById('message-input');  // Get the message input field
    const message = messageInput.value.trim();  // Get the trimmed value from the input field

    // Ensure that the message is not empty and that a user is selected
    if (!message || !selectedUserId) {  
        alert("Please enter a message.");
        return;
    }

    // Send the message to the server
    fetch("/SmessageC/post_message.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `receiver=${selectedUserId}&message=${encodeURIComponent(message)}`  // Send the message data to backend
    })
    .then(response => response.json())  // Parse the JSON response
    .then(result => {
        if (result.status === 'success') {
            const msgDate = new Date();  // Get the current timestamp for the new message
            const currentMessageMinute = msgDate.getHours() * 60 + msgDate.getMinutes();  // Get the minute of the day

            // Check if the message should have a timestamp (if it's in a new minute)
            if (lastSentMessageMinute !== currentMessageMinute) {
                const msgTimeString = msgDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                // Create a separator element to show the timestamp
                const separatorElement = document.createElement('div');
                separatorElement.classList.add('separator', 'separator-centered');
                separatorElement.innerHTML = `<small class="timestamp">${msgTimeString}</small>`;
                MessageBox.appendChild(separatorElement);

                // Update the last sent message minute
                lastSentMessageMinute = currentMessageMinute;
            }

            // Create a new message element
            const messageElement = document.createElement('div');
            messageElement.classList.add('message', 'sent');  // Mark the message as sent
            messageElement.innerHTML = ` 
                <p><strong>You:</strong> ${message}</p>
            `;

            // Append the message to the message box and scroll to the bottom
            MessageBox.appendChild(messageElement);
            MessageBox.scrollTop = MessageBox.scrollHeight;

            // Clear the input field for the next message
            messageInput.value = '';

            // Update the recent message for the selected user
            updateRecentMessageForUser(selectedUserId, message);
        } else {
            console.error("Error sending message:", result.message);  // Handle sending error
        }
    })
    .catch(error => console.error("Error sending message:", error));  // Catch network errors
}


// Function to update the recent message for the selected user
function updateRecentMessageForUser(userId, message, isConversationOpen) {
    const userItem = document.querySelector(`.user-item[data-user-id="${userId}"]`);
    const recentMessageElement = userItem.querySelector('.recent-message');
    const messageDateElement = userItem.querySelector('.message-date');
    const blueDotElement = userItem.querySelector('.blue-dot');
    
    // Get the current time for the message
    const messageDate = new Date();
    const formattedDate = messageDate.toLocaleString(); // Format the date as per local timezone
    messageDateElement.textContent = formattedDate;

    if (isConversationOpen) {
        // If the conversation is open, show the actual messages and hide the recent message notification
        recentMessageElement.textContent = ""; // Clear the recent message
        blueDotElement.classList.add('d-none'); // Hide the blue dot
        userItem.classList.remove('border-blue'); // Remove the blue border
    } else {
        // If the conversation is not open, show the "New Message" notification and blue dot
        recentMessageElement.textContent = message;
        blueDotElement.classList.remove('d-none'); // Show the blue dot
        userItem.classList.add('border-blue'); // Add blue border for unread messages
    }
}


// Switch back to user list view
function backToUserList() {
    document.getElementById('user-list').classList.remove('d-none');
    document.getElementById('conversation-view').classList.add('d-none');
    selectedUserId = null;  // Reset selected user
}
