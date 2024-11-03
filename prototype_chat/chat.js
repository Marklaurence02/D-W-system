document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('register-form');
    const loginForm = document.getElementById('login-form');
    const chatContainer = document.getElementById('chat-container');
    const chatBox = document.getElementById('chat-box');
    const sendMessageBtn = document.getElementById('send-message');
    const messageInput = document.getElementById('message');
    const receiverInput = document.getElementById('receiver');

    registerForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const username = document.getElementById('reg-username').value.trim();
        const password = document.getElementById('reg-password').value.trim();

        const formData = new FormData();
        formData.append('username', username);
        formData.append('password', password);

        const response = await fetch('register.php', { method: 'POST', body: formData });
        alert(await response.text());
    });

    loginForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const username = document.getElementById('login-username').value.trim();
        const password = document.getElementById('login-password').value.trim();

        const formData = new FormData();
        formData.append('username', username);
        formData.append('password', password);

        const response = await fetch('login.php', { method: 'POST', body: formData });
        const message = await response.text();
        alert(message);
        if (message === 'Login successful!') {
            document.getElementById('login-register').style.display = 'none';
            chatContainer.style.display = 'block';
            fetchMessages();
        }
    });

    async function fetchMessages() {
        const receiver = receiverInput.value.trim();
        if (receiver) {
            const response = await fetch(`get_messages.php?receiver=${receiver}`);
            const messages = await response.json();
            chatBox.innerHTML = '';
            messages.reverse().forEach(msg => {
                chatBox.innerHTML += `<p><strong>${msg.sender}:</strong> ${msg.message}</p>`;
            });
        }
    }

    sendMessageBtn.addEventListener('click', async () => {
        const receiver = receiverInput.value.trim();
        const message = messageInput.value.trim();
        if (receiver && message) {
            const formData = new FormData();
            formData.append('receiver', receiver);
            formData.append('message', message);

            await fetch('post_message.php', { method: 'POST', body: formData });
            messageInput.value = '';
            fetchMessages();
        }
    });

    setInterval(fetchMessages, 3000);
});
