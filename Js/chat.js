// // Declare global variables at the top
// let selectedUserId = null;
// let currentUserId = null;
// let currentUserRole = null;
// const unreadMessageCounts = {};
// const chatBox = document.getElementById('chat-box');

// // Initialize DOM elements after the document is loaded
// document.addEventListener('DOMContentLoaded', function() {
//     const chatBubble = document.getElementById('chat-bubble');
//     const chatModal = document.getElementById('chat-modal');
//     const messageForm = document.getElementById('message-form');
//     const messageInput = document.getElementById('message');
//     const userBubblesContainer = document.getElementById('user-bubbles');
//     const closeChatButton = document.getElementById('close-chat');

//     // Get the current user's ID and role
//     async function getCurrentUserIdAndRole() {
//         try {
//             const response = await fetch('/messagecontrol/get_current_user.php');
//             if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

//             const data = await response.json();
//             if (data.user_id) {
//                 currentUserId = data.user_id;
//                 currentUserRole = data.role;
//             } else {
//                 console.error('Error from server:', data.error || 'Unknown error');
//                 alert(`Error fetching current user: ${data.error || 'Unknown error'}`);
//             }
//         } catch (error) {
//             console.error('Error fetching current user data:', error);
//             alert('An unexpected error occurred while fetching user data.');
//         }
//     }

//     // Load user bubbles based on role
//     function loadUsers() {
//         userBubblesContainer.innerHTML = '<p>Loading users...</p>';
    
//         $.ajax({
//             url: "/messagecontrol/get_users.php",
//             method: "GET",
//             success: function(users) {
//                 if (typeof users === 'string') {
//                     try { users = JSON.parse(users); } 
//                     catch (e) { return alert("Invalid response format from the server."); }
//                 }

//                 userBubblesContainer.innerHTML = ''; // Clear loading message

//                 if (!Array.isArray(users) || users.length === 0) {
//                     userBubblesContainer.innerHTML = '<p>No users available to chat.</p>';
//                     return;
//                 }

//                 users.forEach(user => {
//                     // Filter users based on role
//                     if (
//                         (currentUserRole === 'Admin' && ['Owner', 'Staff'].includes(user.role)) ||
//                         (currentUserRole === 'Owner' && ['Admin', 'Staff'].includes(user.role)) ||
//                         (currentUserRole === 'Staff' && user.role === 'Admin')
//                     ) {
//                         displayUserBubble(user);
//                     }
//                 });
//             },
//             error: function(xhr, status, error) {
//                 console.error("Error fetching users:", error, "Response:", xhr.responseText);
//                 alert("An error occurred while fetching the user list.");
//             }
//         });
//     }

//  // Select messenger name element to update the header dynamically
// const messengerName = document.getElementById('messenger-name');

// function displayUserBubble(user) {
//     if (user.status.toLowerCase() !== 'offline') {
//         const userBubble = document.createElement('div');
//         userBubble.classList.add('user-bubble');
//         userBubble.textContent = user.username[0].toUpperCase();
//         userBubble.title = `${user.username} (${user.role}) - ${user.status}`;
//         userBubble.dataset.userId = user.user_id;

//         userBubble.addEventListener('click', function() {
//             // Update the messenger header with selected user's name
//             messengerName.textContent = user.username;

//             if (selectedUserId && selectedUserId !== user.user_id) {
//                 const previousBubble = document.querySelector(`.user-bubble[data-user-id="${selectedUserId}"]`);
//                 if (previousBubble) {
//                     previousBubble.classList.remove('glow');
//                 }
//             }

//             selectedUserId = user.user_id;

//             document.querySelectorAll('.user-bubble').forEach(b => b.classList.remove('glow'));
//             userBubble.classList.add('glow');
//             chatBox.innerHTML = '';
//             loadMessages();
//             startAutoLoad();
//         });

//         userBubblesContainer.appendChild(userBubble);
//     }
// }

// // Reset messenger header when closing chat or deselecting user
// document.getElementById('close-chat').addEventListener('click', function() {
//     messengerName.textContent = 'Messenger?';
//     selectedUserId = null;
//     document.querySelectorAll('.user-bubble').forEach(b => b.classList.remove('glow'));
//     chatBox.innerHTML = '';
// });

//     // Load messages and update unread badge counts
//     function loadMessages() {
//         if (selectedUserId) {
//             $.ajax({
//                 url: "/messagecontrol/get_messages.php",
//                 method: "GET",
//                 data: { receiver: selectedUserId },
//                 success: function(data) {
//                     try {
//                         const messages = typeof data === 'object' ? data : JSON.parse(data);
//                         if (!Array.isArray(messages)) throw new Error("Invalid format");

//                         let lastFormattedDateTime = '';
//                         chatBox.innerHTML = messages.map(msg => {
//                             const sender = msg.sender_id === currentUserId ? 'You' : msg.first_name;
//                             const messageDate = new Date(msg.timestamp);
//                             const formattedDateTime = messageDate.toLocaleString('en-US', {
//                                 dateStyle: 'short', timeStyle: 'short'
//                             });

//                             if (msg.sender_id !== currentUserId && msg.sender_id !== selectedUserId) {
//                                 unreadMessageCounts[msg.sender_id] = (unreadMessageCounts[msg.sender_id] || 0) + 1;

//                                 const userBubble = document.querySelector(`.user-bubble[data-user-id="${msg.sender_id}"]`);
//                                 if (userBubble) {
//                                     const notificationBadge = userBubble.querySelector('.notification-badge');
//                                     notificationBadge.textContent = unreadMessageCounts[msg.sender_id];
//                                     notificationBadge.style.display = 'flex';
//                                 }
//                             }

//                             let dateSeparator = '';
//                             if (formattedDateTime !== lastFormattedDateTime) {
//                                 dateSeparator = `<div class="message-date">------${formattedDateTime}------</div>`;
//                                 lastFormattedDateTime = formattedDateTime;
//                             }

//                             return `${dateSeparator}
//                                     <div class="${msg.sender_id === currentUserId ? 'sent' : 'received'}">
//                                         <strong>${sender}:</strong> ${msg.message}
//                                     </div>`;
//                         }).join('');
//                         chatBox.scrollTop = chatBox.scrollHeight;
//                     } catch (e) {
//                         console.error("Error parsing messages:", e);
//                         alert("Error displaying messages.");
//                     }
//                 },
//                 error: function(xhr, status, error) {
//                     console.error("Error fetching messages:", error);
//                     alert("Error fetching messages.");
//                 }
//             });
//         }
//     }

//     // Auto-load function with pause on interaction
//     function startAutoLoad() {
//         let autoLoadInterval;
//         let isPaused = false;

//         function startInterval() {
//             if (!autoLoadInterval) {
//                 autoLoadInterval = setInterval(() => {
//                     if (!isPaused && selectedUserId) loadMessages();
//                 }, 1000);
//             }
//         }

//         function stopInterval() {
//             if (autoLoadInterval) clearInterval(autoLoadInterval);
//             autoLoadInterval = null;
//         }

//         chatBox.addEventListener('mouseenter', () => { isPaused = true; stopInterval(); });
//         chatBox.addEventListener('mouseleave', () => { isPaused = false; startInterval(); });
//         chatBox.addEventListener('scroll', () => {
//             isPaused = true;
//             stopInterval();
//             clearTimeout(chatBox.scrollTimeout);
//             chatBox.scrollTimeout = setTimeout(() => { isPaused = false; startInterval(); }, 1000);
//         });

//         startInterval();
//     }

//     // Fetch user ID, role, and load users on chat open
//     getCurrentUserIdAndRole().then(loadUsers);
//     chatBubble.addEventListener('click', () => { chatModal.style.display = chatModal.style.display === 'flex' ? 'none' : 'flex'; });
//     closeChatButton.addEventListener('click', () => { chatModal.style.display = 'none'; });

//     // Send message and reload chat
//     messageForm.addEventListener('submit', function(event) {
//         event.preventDefault();
//         const message = messageInput.value.trim();
//         if (message && selectedUserId) {
//             $.ajax({
//                 url: "/messagecontrol/post_message.php",
//                 method: "POST",
//                 data: { receiver: selectedUserId, message },
//                 success: function() {
//                     messageInput.value = '';
//                     loadMessages();
//                 },
//                 error: function(xhr, status, error) {
//                     console.error("Error sending message:", error);
//                     alert("Error sending message.");
//                 }
//             });
//         } else {
//             alert("Please select a user to chat with.");
//         }
//     });
// });
