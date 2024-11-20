<?php
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
                <div class="welcome-sent p-3">
    Welcome to Dine&Watch! I am your Dine&Watch virtual assistant. I'll be happy to answer your questions. For an uninterrupted conversation with us, please ensure that you have a stable internet connection. Please tell me what you would like to know:
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
function loadMessages() {
    $.ajax({
        url: '/usermessagecontrol/chat.php', // Adjust path to your PHP script
        type: 'GET',
        success: function(response) {
            const messagesData = JSON.parse(response).messages;
            $('#chat-box .history-message').empty(); // Clear previous messages
            
            // Loop through the messages and render each one
            messagesData.forEach(function(message) {
                let senderClass;
                let sideClass;
                
                // Check if the message is from a user or assistant (staff, bot, etc.)
                if (message.sender_role === 'assistant') {
                    // If the sender role is assistant (can be staff or bot)
                    senderClass = 'assistant-message';
                    sideClass = 'right';  // Assistant message should be on the right
                } else {
                    // If it's the user (could be staff too, based on your logic)
                    senderClass = 'user-message';
                    sideClass = 'left';  // User message should be on the left
                }
                
                // Create HTML for each message
                const messageHtml = `<div class="${senderClass} ${sideClass}">${message.message}<div class="timestamp">${message.timestamp}</div></div>`;
                $('#chat-box .history-message').append(messageHtml);
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

    // Function to render messages in the chat box
    function renderMessages() {
        $('#chat-box').find('.user-message, .assistant-message').remove();
        messages.forEach(function(message) {
            const senderClass = message.sender === 'user' ? 'user-message' : 'assistant-message';
            const sideClass = message.sender === 'user' ? 'left' : 'right'; // Left for user, right for assistant
            const messageHtml = `<div class="${senderClass} ${sideClass}">${message.text}<div class="timestamp">${message.sender === 'user' ? 'You' : 'Assistant'}</div></div>`;
            $('#chat-box').append(messageHtml);
        });
        $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight); // Scroll to the bottom
    }

    // Send button click event
    $('#send-btn').click(function() {
        const message = $('#message-input').val();
        if (message.trim() !== '') {
            $('#send-btn').prop('disabled', true); // Disable send button while waiting for response
            
            // Add the user's message immediately to the chat history
            messages.push({ sender: 'user', text: message });
            renderMessages(); // Update the chat box with the user's message

            // Send the message via AJAX to the server
            $.ajax({
                url: '/usermessagecontrol/chat.php',
                type: 'POST',
                data: { message: message },
                success: function(response) {
                    const jsonResponse = JSON.parse(response);
                    // Add the assistant's response to the history after receiving it
                    messages.push({ sender: 'assistant', text: jsonResponse.response });
                    renderMessages(); // Re-render messages with the assistant's response
                    $('#message-input').val(''); // Clear the input field
                    $('#send-btn').prop('disabled', false); // Enable send button after response
                },
                error: function() {
                    alert('Error sending message. Please try again.');
                    $('#send-btn').prop('disabled', false); // Enable send button on error
                }
            });
        }
    });

    // Option button click event (for predefined responses)
    $('.option').click(function() {
        const question = $(this).data('question');
        // Add the user's option (question) to the history
        messages.push({ sender: 'user', text: question });
        $('#message-input').val(question);
        
        // Simulate assistant response
        const botResponse = getBotResponse(question);
        messages.push({ sender: 'assistant', text: botResponse });
        renderMessages(); // Re-render the chat box with both user and assistant messages
        $('#message-input').val(''); // Clear the input field
    });

    // Helper function to generate bot responses
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
    loadMessages();
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


</style>

