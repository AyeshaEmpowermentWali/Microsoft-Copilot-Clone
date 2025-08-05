<?php
require_once 'db.php';
header('Content-Type: application/json');

$user_id = getUserId($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $conversation_id = $input['conversation_id'] ?? null;
    $title = $input['title'] ?? 'Saved Response';
    $content = $input['content'] ?? '';
    
    if (empty($content)) {
        echo json_encode(['success' => false, 'error' => 'Content is required']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO saved_responses (user_id, conversation_id, title, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $conversation_id, $title, $content]);
        
        // Update conversation as saved
        if ($conversation_id) {
            $stmt = $pdo->prepare("UPDATE conversations SET is_saved = TRUE WHERE id = ? AND user_id = ?");
            $stmt->execute([$conversation_id, $user_id]);
        }
        
        echo json_encode(['success' => true, _id]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Response saved successfully']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
