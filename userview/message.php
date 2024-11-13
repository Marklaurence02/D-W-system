<?php
session_start();
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="chat-container">
            <div class="header">Customer Service </div>
            <div id="chat-box" class="message">
                <div class="welcome-message">
                    <div class="welcome-sent">Welcome to Dine&Watch! I am your Dine&Watch virtual assistant. I'll be happy to answer your questions. For an uninterrupted conversation with us, please ensure that you have a stable internet connection. Please tell me what you would like to know:</div>
                </div>
            </div>
            <div class="options row gx-2 gy-2 p-3">
                <div class="col-6 option" data-question="No Refund Policy?">No Refund Policy?</div>
                <div class="col-6 option" data-question="What time Dine&Watch Open">What time Dine&Watch Open</div>
                <div class="col-6 option" data-question="FAQ">FAQ</div>
            </div>
            <div class="input-container">
                <input type="text" id="message-input" class="form-control me-2" placeholder="Type something..." required>
                <button id="send-btn" class="btn btn-primary">Send</button>
            </div>
            <div id="notification-badge " class="notification-badge"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            const messages = [];

            function renderMessages() {
                $('#chat-box').find('.user-message, .assistant-message').remove();

                messages.forEach(function(message) {
                    if (message.sender === 'user') {
                        $('#chat-box').append('<div class="user-message">' + message.text + '<div class="timestamp">You</div></div>');
                    } else {
                        $('#chat-box').append('<div class="assistant-message">' + message.text + '<div class="timestamp">Assistant</div></div>');
                    }
                });
                $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
            }

            $('#send-btn').click(function() {
                const message = $('#message-input').val();
                if (message.trim() !== '') {
                    messages.push({ sender: 'user', text: message });

                    $.ajax({
                        url: '/usermessagecontrol/chat.php',
                        type: 'POST',
                        data: { message: message },
                        success: function(response) {
                            const jsonResponse = JSON.parse(response);
                            messages.push({ sender: 'assistant', text: jsonResponse.response });
                            renderMessages();
                            $('#message-input').val('');
                        },
                        error: function() {
                            alert('Error sending message. Please try again.');
                        }
                    });
                }
            });

            $('.option').click(function() {
                const question = $(this).data('question');
                messages.push({ sender: 'user', text: question });
                $('#message-input').val(question);
                
                const botResponse = getBotResponse(question);
                messages.push({ sender: 'assistant', text: botResponse });
                renderMessages();
                $('#message-input').val('');
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
    display: flex;
    align-items: center;
    justify-content: space-around;    
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

.user-message, .assistant-message {
            padding: 8px;
            border-radius: 10px;
            margin: 5px 0;
            font-size: 14px;
        }

        .user-message {
            background-color: #d1e7dd;
            color: #005bb5;
            text-align: right;
            float: right;
            clear: both;
        }

        .assistant-message {
            background-color: #f8d7da;
            color: #b23b3b;
            text-align: left;
            float: left;
            clear: both;
        }

        .timestamp {
            font-size: 0.8em;
            color: #888;
            margin-left: 5px;
        }
</style>