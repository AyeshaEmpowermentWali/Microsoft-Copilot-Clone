<?php
require_once 'db.php';
header('Content-Type: application/json');

$user_id = getUserId($pdo);

// Handle different API actions
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'history') {
        getHistory($pdo, $user_id);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['message']) || empty(trim($input['message']))) {
        echo json_encode(['success' => false, 'error' => 'Message is required']);
        exit;
    }
    
    $message = trim($input['message']);
    $start_time = microtime(true);
    
    // Get AI response
    $ai_response = getAIResponse($message);
    $response_time = microtime(true) - $start_time;
    
    if ($ai_response) {
        // Save conversation to database
        $stmt = $pdo->prepare("INSERT INTO conversations (user_id, message, response, response_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $message, $ai_response, $response_time]);
        $conversation_id = $pdo->lastInsertId();
        
        // Log API usage
        $stmt = $pdo->prepare("INSERT INTO api_usage (user_id, endpoint, response_time, status_code) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, 'huggingface', $response_time, 200]);
        
        echo json_encode([
            'success' => true,
            'response' => $ai_response,
            'conversation_id' => $conversation_id,
            'response_time' => round($response_time, 3)
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to get AI response']);
    }
}

function getAIResponse($message) {
    // Using Hugging Face Inference API (free tier)
    $api_url = 'https://api-inference.huggingface.co/models/microsoft/DialoGPT-large';
    
    // You can also use other free APIs:
    // OpenAI-compatible APIs, Cohere, or local models
    
    $data = [
        'inputs' => $message,
        'parameters' => [
            'max_length' => 500,
            'temperature' => 0.7,
            'do_sample' => true
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer YOUR_HUGGINGFACE_TOKEN' // Replace with your token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200 && $response) {
        $result = json_decode($response, true);
        if (isset($result[0]['generated_text'])) {
            return $result[0]['generated_text'];
        }
    }
    
    // Fallback response if API fails
    return getFallbackResponse($message);
}

function getFallbackResponse($message) {
    // Simple rule-based responses as fallback
    $message_lower = strtolower($message);
    
    if (strpos($message_lower, 'hello') !== false || strpos($message_lower, 'hi') !== false) {
        return "Hello! I'm your AI assistant. How can I help you today?";
    }
    
    if (strpos($message_lower, 'email') !== false) {
        return "I'd be happy to help you write an email! Please provide me with the details like the recipient, subject, and main points you want to include.";
    }
    
    if (strpos($message_lower, 'code') !== false || strpos($message_lower, 'programming') !== false) {
        return "I can help you with coding! Please share your code or describe the programming problem you're facing, and I'll do my best to assist you.";
    }
    
    if (strpos($message_lower, 'summarize') !== false || strpos($message_lower, 'summary') !== false) {
        return "I can help you summarize text! Please provide the content you'd like me to summarize, and I'll create a concise summary for you.";
    }
    
    // Generic helpful response
    return "Thank you for your message! I'm an AI assistant designed to help with various tasks including writing, coding, analysis, and creative projects. Could you please provide more specific details about what you'd like assistance with?";
}

function getHistory($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT message, response, created_at FROM conversations WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$user_id]);
    $history = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'history' => $history]);
}
?>
