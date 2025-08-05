<?php
require_once 'db.php';
$user_id = getUserId($pdo);

// Get initial prompt if provided
$initial_prompt = isset($_GET['prompt']) ? htmlspecialchars($_GET['prompt']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat - Microsoft Copilot Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .header-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .chat-container {
            flex: 1;
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .sidebar {
            width: 300px;
            background: white;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .sidebar-header h3 {
            color: #1f2937;
            margin-bottom: 15px;
        }

        .template-btn {
            width: 100%;
            padding: 10px;
            margin-bottom: 8px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            text-align: left;
            transition: all 0.3s ease;
        }

        .template-btn:hover {
            background: #e0e7ff;
            border-color: #4f46e5;
        }

        .chat-history {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .history-item {
            padding: 10px;
            margin-bottom: 8px;
            background: #f8fafc;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .history-item:hover {
            background: #e0e7ff;
            border-left-color: #4f46e5;
        }

        .history-item h4 {
            font-size: 0.9rem;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .history-item p {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .main-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .messages-container {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #fafbfc;
        }

        .message {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .message.user {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            flex-shrink: 0;
        }

        .user .message-avatar {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }

        .ai .message-avatar {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .message-content {
            max-width: 70%;
            padding: 15px 20px;
            border-radius: 18px;
            position: relative;
        }

        .user .message-content {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .ai .message-content {
            background: white;
            border: 1px solid #e5e7eb;
            color: #1f2937;
            border-bottom-left-radius: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .message-actions {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }

        .action-btn {
            padding: 4px 8px;
            background: #f3f4f6;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: #e5e7eb;
        }

        .input-container {
            padding: 20px;
            background: white;
            border-top: 1px solid #e5e7eb;
        }

        .input-wrapper {
            display: flex;
            gap: 12px;
            align-items: flex-end;
            max-width: 100%;
        }

        .message-input {
            flex: 1;
            min-height: 50px;
            max-height: 120px;
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 25px;
            resize: none;
            font-family: inherit;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .message-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .send-btn {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .send-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3);
        }

        .send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .typing-indicator {
            display: none;
            padding: 15px 20px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            border-bottom-left-radius: 5px;
            max-width: 70%;
        }

        .typing-dots {
            display: flex;
            gap: 4px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: #9ca3af;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-10px); }
        }

        .welcome-message {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .welcome-message h2 {
            color: #1f2937;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .chat-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: 200px;
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }
            
            .message-content {
                max-width: 85%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🤖 AI Copilot Assistant</h1>
        <div class="header-buttons">
            <a href="history.php" class="btn btn-secondary">History</a>
            <a href="index.php" class="btn btn-secondary">Home</a>
        </div>
    </div>

    <div class="chat-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>Quick Templates</h3>
                <button class="template-btn" onclick="useTemplate('Write a professional email about: ')">📧 Email Writer</button>
                <button class="template-btn" onclick="useTemplate('Help me with this code: ')">💻 Code Helper</button>
                <button class="template-btn" onclick="useTemplate('Summarize this text: ')">📄 Summarizer</button>
                <button class="template-btn" onclick="useTemplate('Create a creative story about: ')">✨ Creative Writer</button>
                <button class="template-btn" onclick="useTemplate('Explain this concept: ')">🎓 Explainer</button>
            </div>
            
            <div class="chat-history">
                <h4 style="margin-bottom: 15px; color: #1f2937;">Recent Chats</h4>
                <div id="historyList"></div>
            </div>
        </div>

        <div class="main-chat">
            <div class="messages-container" id="messagesContainer">
                <div class="welcome-message">
                    <h2>Welcome to AI Copilot Assistant!</h2>
                    <p>I'm here to help you with writing, coding, analysis, and creative tasks. What can I assist you with today?</p>
                </div>
            </div>

            <div class="typing-indicator" id="typingIndicator">
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>

            <div class="input-container">
                <div class="input-wrapper">
                    <textarea 
                        id="messageInput" 
                        class="message-input" 
                        placeholder="Type your message here..."
                        rows="1"
                    ><?php echo $initial_prompt; ?></textarea>
                    <button id="sendBtn" class="send-btn" onclick="sendMessage()">➤</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let conversationHistory = [];

        // Auto-resize textarea
        const messageInput = document.getElementById('messageInput');
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Send message on Enter (but allow Shift+Enter for new line)
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // If there's an initial prompt, send it automatically
        if (messageInput.value.trim()) {
            setTimeout(() => sendMessage(), 500);
        }

        function useTemplate(template) {
            messageInput.value = template;
            messageInput.focus();
        }

        async function sendMessage() {
            const message = messageInput.value.trim();
            if (!message) return;

            const sendBtn = document.getElementById('sendBtn');
            const messagesContainer = document.getElementById('messagesContainer');
            const typingIndicator = document.getElementById('typingIndicator');

            // Clear welcome message
            const welcomeMessage = messagesContainer.querySelector('.welcome-message');
            if (welcomeMessage) {
                welcomeMessage.remove();
            }

            // Add user message
            addMessage(message, 'user');
            messageInput.value = '';
            messageInput.style.height = 'auto';
            
            // Disable send button and show typing indicator
            sendBtn.disabled = true;
            typingIndicator.style.display = 'block';
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                
                // Hide typing indicator
                typingIndicator.style.display = 'none';
                
                if (data.success) {
                    addMessage(data.response, 'ai', data.conversation_id);
                    loadHistory(); // Refresh history
                } else {
                    addMessage('Sorry, I encountered an error. Please try again.', 'ai');
                }
            } catch (error) {
                typingIndicator.style.display = 'none';
                addMessage('Sorry, I encountered a connection error. Please try again.', 'ai');
            }

            sendBtn.disabled = false;
        }

        function addMessage(content, sender, conversationId = null) {
            const messagesContainer = document.getElementById('messagesContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;

            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.textContent = sender === 'user' ? 'U' : 'AI';

            const messageContent = document.createElement('div');
            messageContent.className = 'message-content';
            messageContent.innerHTML = formatMessage(content);

            messageDiv.appendChild(avatar);
            messageDiv.appendChild(messageContent);

            // Add action buttons for AI messages
            if (sender === 'ai' && conversationId) {
                const actionsDiv = document.createElement('div');
                actionsDiv.className = 'message-actions';
                
                const copyBtn = document.createElement('button');
                copyBtn.className = 'action-btn';
                copyBtn.textContent = '📋 Copy';
                copyBtn.onclick = () => copyToClipboard(content);
                
                const saveBtn = document.createElement('button');
                saveBtn.className = 'action-btn';
                saveBtn.textContent = '💾 Save';
                saveBtn.onclick = () => saveResponse(conversationId, content);
                
                actionsDiv.appendChild(copyBtn);
                actionsDiv.appendChild(saveBtn);
                messageContent.appendChild(actionsDiv);
            }

            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function formatMessage(content) {
            // Basic formatting for code blocks and line breaks
            return content
                .replace(/```([\s\S]*?)```/g, '<pre style="background: #f3f4f6; padding: 10px; border-radius: 5px; margin: 10px 0; overflow-x: auto;"><code>$1</code></pre>')
                .replace(/`([^`]+)`/g, '<code style="background: #f3f4f6; padding: 2px 4px; border-radius: 3px;">$1</code>')
                .replace(/\n/g, '<br>');
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show temporary feedback
                const feedback = document.createElement('div');
                feedback.textContent = 'Copied!';
                feedback.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 10px 20px; border-radius: 5px; z-index: 1000;';
                document.body.appendChild(feedback);
                setTimeout(() => feedback.remove(), 2000);
            });
        }

        async function saveResponse(conversationId, content) {
            try {
                const response = await fetch('save_response.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        conversation_id: conversationId,
                        content: content 
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    const feedback = document.createElement('div');
                    feedback.textContent = 'Response saved!';
                    feedback.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #4f46e5; color: white; padding: 10px 20px; border-radius: 5px; z-index: 1000;';
                    document.body.appendChild(feedback);
                    setTimeout(() => feedback.remove(), 2000);
                }
            } catch (error) {
                console.error('Error saving response:', error);
            }
        }

        async function loadHistory() {
            try {
                const response = await fetch('api.php?action=history');
                const data = await response.json();
                
                const historyList = document.getElementById('historyList');
                historyList.innerHTML = '';
                
                data.history.forEach(item => {
                    const historyItem = document.createElement('div');
                    historyItem.className = 'history-item';
                    historyItem.innerHTML = `
                        <h4>${item.message.substring(0, 30)}...</h4>
                        <p>${new Date(item.created_at).toLocaleDateString()}</p>
                    `;
                    historyItem.onclick = () => {
                        messageInput.value = item.message;
                        messageInput.focus();
                    };
                    historyList.appendChild(historyItem);
                });
            } catch (error) {
                console.error('Error loading history:', error);
            }
        }

        // Load history on page load
        loadHistory();
    </script>
</body>
</html>
