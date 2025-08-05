<?php
require_once 'db.php';
$user_id = getUserId($pdo);

// Get all conversations
$stmt = $pdo->prepare("SELECT * FROM conversations WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$conversations = $stmt->fetchAll();

// Get saved responses
$stmt = $pdo->prepare("SELECT * FROM saved_responses WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$saved_responses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat History - AI Copilot Assistant</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .nav-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
        }

        .btn {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .tabs {
            display: flex;
            background: white;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .tab {
            flex: 1;
            padding: 15px 20px;
            background: #f8fafc;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .tab.active {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .conversation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
        }

        .conversation-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .conversation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .conversation-header {
            display: flex;
            justify-content: between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .conversation-date {
            color: #6b7280;
            font-size: 0.9rem;
            margin-left: auto;
        }

        .message-preview {
            background: #f8fafc;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 3px solid #4f46e5;
        }

        .message-label {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .message-text {
            color: #4b5563;
            line-height: 1.5;
            max-height: 60px;
            overflow: hidden;
            position: relative;
        }

        .message-text::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 30px;
            height: 20px;
            background: linear-gradient(to right, transparent, #f8fafc);
        }

        .response-preview {
            background: #f0fdf4;
            padding: 12px;
            border-radius: 8px;
            border-left: 3px solid #10b981;
        }

        .conversation-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
        }

        .action-btn {
            padding: 6px 12px;
            background: #f3f4f6;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: #e5e7eb;
        }

        .action-btn.save {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .action-btn.save:hover {
            background: #bfdbfe;
        }

        .action-btn.delete {
            background: #fee2e2;
            color: #dc2626;
        }

        .action-btn.delete:hover {
            background: #fecaca;
        }

        .saved-item {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }

        .saved-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .saved-title {
            font-weight: 600;
            color: #1f2937;
            font-size: 1.1rem;
        }

        .saved-content {
            color: #4b5563;
            line-height: 1.6;
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #10b981;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state h3 {
            color: #1f2937;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .conversation-grid {
                grid-template-columns: 1fr;
            }
            
            .tabs {
                flex-direction: column;
            }
            
            .nav-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 Chat History & Saved Responses</h1>
        <p>Manage your conversations and saved AI responses</p>
        <div class="nav-buttons">
            <a href="chat.php" class="btn">💬 New Chat</a>
            <a href="index.php" class="btn">🏠 Home</a>
        </div>
    </div>

    <div class="container">
        <div class="tabs">
            <button class="tab active" onclick="switchTab('conversations')">
                💬 Conversations (<?php echo count($conversations); ?>)
            </button>
            <button class="tab" onclick="switchTab('saved')">
                💾 Saved Responses (<?php echo count($saved_responses); ?>)
            </button>
        </div>

        <div id="conversations" class="tab-content active">
            <?php if (empty($conversations)): ?>
                <div class="empty-state">
                    <h3>No conversations yet</h3>
                    <p>Start chatting with the AI assistant to see your conversation history here.</p>
                    <a href="chat.php" class="btn" style="display: inline-block; margin-top: 20px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">Start First Conversation</a>
                </div>
            <?php else: ?>
                <div class="conversation-grid">
                    <?php foreach ($conversations as $conv): ?>
                        <div class="conversation-card">
                            <div class="conversation-header">
                                <div class="conversation-date">
                                    <?php echo date('M j, Y - g:i A', strtotime($conv['created_at'])); ?>
                                </div>
                            </div>
                            
                            <div class="message-preview">
                                <div class="message-label">Your Message:</div>
                                <div class="message-text"><?php echo htmlspecialchars(substr($conv['message'], 0, 100)); ?><?php echo strlen($conv['message']) > 100 ? '...' : ''; ?></div>
                            </div>
                            
                            <div class="response-preview">
                                <div class="message-label">AI Response:</div>
                                <div class="message-text"><?php echo htmlspecialchars(substr($conv['response'], 0, 100)); ?><?php echo strlen($conv['response']) > 100 ? '...' : ''; ?></div>
                            </div>
                            
                            <div class="conversation-actions">
                                <button class="action-btn" onclick="copyToClipboard('<?php echo htmlspecialchars($conv['response'], ENT_QUOTES); ?>')">📋 Copy</button>
                                <button class="action-btn save" onclick="saveResponse(<?php echo $conv['id']; ?>, '<?php echo htmlspecialchars($conv['response'], ENT_QUOTES); ?>')">💾 Save</button>
                                <button class="action-btn" onclick="continueChat('<?php echo htmlspecialchars($conv['message'], ENT_QUOTES); ?>')">💬 Continue</button>
                                <button class="action-btn delete" onclick="deleteConversation(<?php echo $conv['id']; ?>)">🗑️ Delete</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div id="saved" class="tab-content">
            <?php if (empty($saved_responses)): ?>
                <div class="empty-state">
                    <h3>No saved responses yet</h3>
                    <p>Save important AI responses from your conversations to access them quickly later.</p>
                </div>
            <?php else: ?>
                <?php foreach ($saved_responses as $saved): ?>
                    <div class="saved-item">
                        <div class="saved-header">
                            <div class="saved-title"><?php echo htmlspecialchars($saved['title']); ?></div>
                            <div class="conversation-date">
                                <?php echo date('M j, Y - g:i A', strtotime($saved['created_at'])); ?>
                            </div>
                        </div>
                        
                        <div class="saved-content">
                            <?php echo nl2br(htmlspecialchars($saved['content'])); ?>
                        </div>
                        
                        <div class="conversation-actions">
                            <button class="action-btn" onclick="copyToClipboard('<?php echo htmlspecialchars($saved['content'], ENT_QUOTES); ?>')">📋 Copy</button>
                            <button class="action-btn delete" onclick="deleteSaved(<?php echo $saved['id']; ?>)">🗑️ Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showNotification('Copied to clipboard!', 'success');
            });
        }

        function continueChat(message) {
            window.location.href = 'chat.php?prompt=' + encodeURIComponent(message);
        }

        async function saveResponse(conversationId, content) {
            const title = prompt('Enter a title for this saved response:');
            if (!title) return;

            try {
                const response = await fetch('save_response.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        conversation_id: conversationId,
                        title: title,
                        content: content
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showNotification('Response saved successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            } catch (error) {
                showNotification('Error saving response', 'error');
            }
        }

        async function deleteConversation(id) {
            if (!confirm('Are you sure you want to delete this conversation?')) return;

            try {
                const response = await fetch('delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        type: 'conversation',
                        id: id
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showNotification('Conversation deleted', 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            } catch (error) {
                showNotification('Error deleting conversation', 'error');
            }
        }

        async function deleteSaved(id) {
            if (!confirm('Are you sure you want to delete this saved response?')) return;

            try {
                const response = await fetch('delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        type: 'saved',
                        id: id
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showNotification('Saved response deleted', 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            } catch (error) {
                showNotification('Error deleting saved response', 'error');
            }
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 25px;
                border-radius: 5px;
                color: white;
                font-weight: 600;
                z-index: 1000;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
            `;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }
    </script>
</body>
</html>
