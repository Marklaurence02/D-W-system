<?php
session_name("user_session");
session_start();
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="chat-container">
        <div class="header">Customer Service</div>
        <div id="chat-box" class="message">
            <div class="welcome-message">
                <div class="history-message">
                    <!-- Dynamic chat messages will load here -->
                </div>

            </div>
        </div>
        <div class="options row gx-2 gy-2 p-3">
    <div class="col-6 option" data-question="No Refund Policy?">No Refund Policy?</div>
    <div class="col-6 option" data-question="What time Dine&Watch Open">What time Dine&Watch Open</div>
    <div class="col-12 text-center option" data-question="FAQ">FAQ</div>
</div>

        <div class="input-container">
            <input type="text" id="message-input" class="form-control me-2" placeholder="Type something..." required>
            <button id="send-btn" class="btn btn-primary">Send</button>
        </div>
    </div>
</div>

<script>
function loadMessages() {
    $.ajax({
        url: '/usermessagecontrol/chat.php', // Adjust path to your PHP script
        type: 'GET',
        success: function(response) {
            const messagesData = JSON.parse(response).messages;
            $('#chat-box .history-message').empty(); // Clear previous messages
            
            // Check if there are no messages and append the welcome message
            if (messagesData.length === 0) {
                const welcomeMessageHtml = `<div class="assistant-message right">Welcome to Dine&Watch! I am your Dine&Watch virtual assistant. I'll be happy to answer your questions. For an uninterrupted conversation with us, please ensure that you have a stable internet connection. Please tell me what you would like to know:<div class="timestamp">${new Date().toLocaleString()}</div></div>`;
                $('#chat-box .history-message').append(welcomeMessageHtml);
            }

            let lastMessageMinute = null; // Track the last message minute

            // Loop through the messages and render each one
            messagesData.forEach(function(message) {
                let senderClass;
                let sideClass;
                const messageTime = new Date(message.timestamp);
                const msgTimeString = messageTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }).toLowerCase();

                // Add a separator for each message
                const separatorElement = document.createElement('div');
                separatorElement.classList.add('separator', 'separator-centered');
                separatorElement.innerHTML = `------${msgTimeString}----`;
                $('#chat-box .history-message').append(separatorElement);

                // Check if the message is from a user or assistant (staff, bot, etc.)
                if (message.sender_role === 'assistant') {
                    senderClass = 'assistant-message';
                    sideClass = 'right';  // Assistant message should be on the right
                } else {
                    senderClass = 'user-message';
                    sideClass = 'left';  // User message should be on the left
                }
                
                // Create HTML for each message
                const messageHtml = `<div class="${senderClass} ${sideClass}">${message.message}<div class="timestamp">${message.timestamp}</div></div>`;
                $('#chat-box .history-message').append(messageHtml);

                // Add a separator line after each message
                $('#chat-box .history-message').append('<div class="message-separator"></div>');
            });

            // Scroll to the bottom to show the latest message
            $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
            
            // Check for unread messages (any message from user)
            if (messagesData.some(msg => msg.sender_role !== 'assistant')) {
                $('#notification-badge').show();
            } else {
                $('#notification-badge').hide();
            }
        },
        error: function() {
            alert('Error loading chat messages.');
        }
    });
}

$(document).ready(function() {
    let messages = [];
    let isStaffOnline = false; // Track staff online status

    function renderMessages() {
        $('#chat-box').find('.user-message, .assistant-message, .separator').remove();
        messages.forEach(function(message) {
            const senderClass = message.sender === 'user' ? 'user-message' : 'assistant-message';
            const sideClass = message.sender === 'user' ? 'left' : 'right';
            

            // Format the timestamp to show only the time
            const messageTime = new Date(message.timestamp);
            const formattedTime = messageTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }).toLowerCase();

            // Include sender label and timestamp
            const senderLabel = message.sender === 'user' ? 'You' : 'Assistant';
            const messageHtml = `<div class="${senderClass} ${sideClass}">${message.text}<div class="timestamp">${senderLabel} - ${formattedTime}</div></div>`;
            
            $('#chat-box').append(messageHtml);
        });
        $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
    }

    function fetchMessages() {
        $.ajax({
            url: '/usermessagecontrol/chat.php',
            type: 'GET',
            success: function(response) {
                const data = JSON.parse(response);
                messages = data.messages.map(msg => ({
                    sender: msg.sender_role,
                    text: msg.message,
                    timestamp: msg.timestamp
                }));
                isStaffOnline = data.isStaffOnline; // Update staff online status
                renderMessages();

                // Start polling only if staff is online
                if (isStaffOnline) {
                    setTimeout(fetchMessages, 10000); // Poll every 10 seconds
                }
            },
            error: function() {
                console.error('Error fetching messages.');
            }
        });
    }

    $('#send-btn').click(function() {
        const message = $('#message-input').val();
        if (message.trim() !== '') {
            $('#send-btn').prop('disabled', true);
            $('#message-input').prop('disabled', true); // Disable input field

            const currentTime = new Date().toISOString(); // Get current date and time
            messages.push({ sender: 'user', text: message, timestamp: currentTime });
            renderMessages();

            $.ajax({
                url: '/usermessagecontrol/chat.php',
                type: 'POST',
                data: { message: message },
                success: function(response) {
                    const jsonResponse = JSON.parse(response);
                    const assistantTime = new Date().toISOString(); // Use current date and time for assistant
                    messages.push({ sender: 'assistant', text: jsonResponse.response, timestamp: assistantTime });
                    renderMessages();
                    $('#message-input').val('');
                    $('#send-btn').prop('disabled', false);
                    $('#message-input').prop('disabled', false);
                },
                error: function() {
                    alert('Error sending message. Please try again.');
                    $('#send-btn').prop('disabled', false);
                    $('#message-input').prop('disabled', false);
                }
            });
        }
    });

    $('.option').click(function() {
        const question = $(this).data('question');
        const currentTime = new Date().toISOString(); // Get current date and time
        messages.push({ sender: 'user', text: question, timestamp: currentTime });
        $('#message-input').val(question);

        const botResponse = getBotResponse(question);
        const assistantTime = new Date().toISOString(); // Use current date and time for assistant
        messages.push({ sender: 'assistant', text: botResponse, timestamp: assistantTime });
        renderMessages();
        $('#message-input').val('');

        // Add a 10-second delay before reloading messages
        setTimeout(fetchMessages, 10000); // 10000 milliseconds = 10 seconds
    });

    function getBotResponse(question) {
        switch (question) {
            case "No Refund Policy?":
                return "Our policy states that all sales are final. Please refer to our terms and conditions for more details.";
            case "What time Dine&Watch Open":
                return "Dine&Watch opens at 11 AM and closes at 11 PM daily.";
            case "FAQ":
                return "You can find answers to common questions in our FAQ section on our website.";
            default:
                return "I'm sorry, I didn't understand that. Can you please rephrase your question?";
        }
    }

    // Initial message loading
    fetchMessages();
});

</script>




<style>
.chat-container {
    width: 100%;
    max-width: 900px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.header {
    background-color: #005bb5;
    padding: 16px;
    color: #ffffff;
    text-align: center;
    font-size: 20px;
}

.message {
    padding: 20px;
    height: 300px;
    overflow-y: scroll;
    background-color: #f9f9f9;
}

.welcome-message {
    margin-bottom: 20px;
}

.options .option {
    padding: 10px;
    background-color: #007bff;
    color: #fff;
    cursor: pointer;
    border-radius: 4px;
    margin: 5px 0;
    text-align: center; /* Center text */
    display: flex;
    justify-content: center; /* Center content horizontally */
    align-items: center; /* Center content vertically */
}

.options .option:hover {
    background-color: #005bb5;
}

/* Align input and button in a row */
.input-container {
    display: flex;
    align-items: center; /* Vertically align input and button */
    justify-content: space-between;
    padding: 10px;
    background-color: #f1f1f1;
}

/* Ensure the input field and button are aligned in a single row */
.input-container input {
    width: 80%; /* Make input take up most of the space */
}

.input-container button {
    width: 18%; /* Make the button take up a small amount of space */
    margin-left: 5px; /* Add a small space between the input and button */
}

.user-message {
    background-color: #d0f8ff;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 10px;
    margin-left: 20px;  /* Left margin for user message */
    float: right;  /* Ensure the message floats to the left */
    clear: both;  /* Clear any float issues */
    max-width: 70%; /* Optional: Control the width of the user message */
}

.assistant-message {
    background-color: #d1e7dd;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 10px;
    margin-right: 20px;  /* Right margin for assistant message */
    float: left;  /* Ensure the message floats to the right */
    clear: both;  /* Clear any float issues */
    max-width: 70%; /* Optional: Control the width of the assistant message */
}

.timestamp {
    font-size: 0.8em;
    color: gray;
    text-align: right;
}

#chat-box .history-message {
    display: block;
    padding: 10px;
}
.welcome-sent{
    background-color: #d1e7dd;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 10px;
    margin-right: 20px;  /* Right margin for assistant message */
    float: left;  /* Ensure the message floats to the right */
    clear: both;  /* Clear any float issues */
    max-width: 70%; /* Optional: Control the width of the assistant message */
}

.message-separator {
    border-top: 1px solid #ccc; /* Light gray line */
    margin: 10px 0; /* Space around the line */
}

.separator {
    text-align: center;
    margin: 10px 0;
    font-size: 0.9em;
    color: #555;
}

</style>

