<?php
require_once 'db.php';
header('Content-Type: application/json');

$user_id = getUserId($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $type = $input['type'] ?? '';
    $id = $input['id'] ?? 0;
    
    if (!$type || !$id) {
        echo json_encode(['success' => false, 'error' => 'Type and ID are required']);
        exit;
    }
    
    try {
        if ($type === 'conversation') {
            $stmt = $pdo->prepare("DELETE FROM conversations WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
        } elseif ($type === 'saved') {
            $stmt = $pdo->prepare("DELETE FROM saved_responses WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid type']);
            exit;
        }
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => ucfirst($type) . ' deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Item not found or access denied']);
        }
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
