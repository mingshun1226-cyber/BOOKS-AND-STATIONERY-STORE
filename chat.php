<?php
require '_base.php';
auth();
$_title = "Live Chat";
require '_head.php';
?>

<div style="max-width: 600px; margin: 0 auto; border: 1px solid #ccc; padding: 10px; border-radius: 8px;">
    <h2>💬 Live Chat</h2>
    
    <div id="chat-box" style="height: 300px; overflow-y: scroll; border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
    </div>

    <form id="chat-form" style="display: flex; gap: 10px;">
        <input type="text" id="message-input" placeholder="Type your message..." style="flex-grow: 1; padding: 8px;" required autocomplete="off">
        <button type="submit" style="padding: 8px 15px; background: #3498db; color: white; border: none; cursor: pointer;">Send</button>
    </form>
</div>

<script>
const chatBox = document.getElementById('chat-box');
const chatForm = document.getElementById('chat-form');
const messageInput = document.getElementById('message-input');

// Function to fetch new messages
function loadMessages() {
    fetch('chat_fetch.php')
        .then(response => response.text())
        .then(html => {
            
            let isScrolledToBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 50;
            
            chatBox.innerHTML = html;
            
            if (isScrolledToBottom) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });
}

chatForm.addEventListener('submit', function(e) {
    e.preventDefault(); 
    
    let formData = new FormData();
    formData.append('message', messageInput.value);
    
    fetch('chat_send.php', {
        method: 'POST',
        body: formData
    }).then(() => {
        messageInput.value = ''; 
        loadMessages(); 
        setTimeout(() => chatBox.scrollTop = chatBox.scrollHeight, 100); 
    });
});

setInterval(loadMessages, 2000);

loadMessages();
</script>

<?php require '_foot.php'; ?>