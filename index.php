<?php
require_once 'db.php';
$user_id = getUserId($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Microsoft Copilot Clone - AI Assistant</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 1200px;
            width: 90%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .main-content {
            padding: 50px 40px;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: white;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #1f2937;
        }

        .feature-card p {
            color: #6b7280;
            line-height: 1.6;
        }

        .cta-section {
            text-align: center;
            background: #f8fafc;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(79, 70, 229, 0.4);
        }

        .examples {
            margin-top: 40px;
        }

        .examples h3 {
            text-align: center;
            margin-bottom: 30px;
            color: #1f2937;
            font-size: 1.8rem;
        }

        .example-prompts {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .example-prompt {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #4f46e5;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .example-prompt:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .example-prompt p {
            color: #4b5563;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .main-content {
                padding: 30px 20px;
            }
            
            .features {
                grid-template-columns: 1fr;
            }
        }

        .footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🤖 AI Copilot Assistant</h1>
            <p>Your intelligent companion for productivity and creativity</p>
        </div>

        <div class="main-content">
            <div class="features">
                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <h3>Smart Conversations</h3>
                    <p>Engage in natural conversations with our AI assistant. Get answers, explanations, and creative solutions to your questions.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h3>Content Generation</h3>
                    <p>Generate emails, articles, code, and creative content. Our AI helps you write better and faster.</p>
                </div>

                <div class="feature-card">
                    <div-icon">🔧</div>
                    <h3>Task Automation</h3>
                    <p>Automate repetitive tasks with intelligent suggestions and templates for common workflows.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📚</div>
                    <h3>Knowledge Base</h3>
                    <p>Access vast knowledge across multiple domains. Get accurate information and detailed explanations.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">💾</div>
                    <h3>Save & History</h3>
                    <p>Keep track of your conversations and save important responses for future reference.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Responsive Design</h3>
                    <p>Works seamlessly across all devices - desktop, tablet, and mobile. AI assistance anywhere, anytime.</p>
                </div>
            </div>

            <div class="cta-section">
                <h2 style="margin-bottom: 20px; color: #1f2937;">Ready to boost your productivity?</h2>
                <a href="#" class="cta-button" onclick="redirectToChat()">Start Chatting Now</a>
            </div>

            <div class="examples">
                <h3>Try these example prompts:</h3>
                <div class="example-prompts">
                    <div class="example-prompt" onclick="redirectToChatWithPrompt('Write a professional email to schedule a meeting')">
                        <p>"Write a professional email to schedule a meeting"</p>
                    </div>
                    <div class="example-prompt" onclick="redirectToChatWithPrompt('Explain quantum computing in simple terms')">
                        <p>"Explain quantum computing in simple terms"</p>
                    </div>
                    <div class="example-prompt" onclick="redirectToChatWithPrompt('Help me debug this JavaScript code')">
                        <p>"Help me debug this JavaScript code"</p>
                    </div>
                    <div class="example-prompt" onclick="redirectToChatWithPrompt('Create a marketing strategy for a startup')">
                        <p>"Create a marketing strategy for a startup"</p>
                    </div>
                    <div class="example-prompt" onclick="redirectToChatWithPrompt('Summarize the latest AI trends')">
                        <p>"Summarize the latest AI trends"</p>
                    </div>
                    <div class="example-prompt" onclick="redirectToChatWithPrompt('Write a creative story about space exploration')">
                        <p>"Write a creative story about space exploration"</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2024 AI Copilot Assistant. Powered by advanced AI technology.</p>
        </div>
    </div>

    <script>
        function redirectToChat() {
            window.location.href = 'chat.php';
        }

        function redirectToChatWithPrompt(prompt) {
            window.location.href = 'chat.php?prompt=' + encodeURIComponent(prompt);
        }

        // Add smooth scrolling and animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.feature-card, .example-prompt');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>
