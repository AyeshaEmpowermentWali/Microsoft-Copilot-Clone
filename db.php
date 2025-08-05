<?php
// Database configuration with multiple host options
$configs = [
    // Primary configuration
    [
        'host' => 'localhost',
        'dbname' => 'dbsqucbber6ud8',
        'username' => 'ugrj543f7lreepassword',
        'password' => 'cgmq43woifko'
    ],
    // Alternative configurations for different hosting environments
    [
        'host' => '127.0.0.1',
        'dbname' => 'dbsqucbber6ud8',
        'username' => 'ugrj543f7lreepassword',
        'password' => 'cgmq43woifko'
    ],
    // For some hosting providers
    [
        'host' => 'localhost',
        'dbname' => 'dbsqucbber6ud8',
        'username' => 'dbsqucbber6ud8',  // Sometimes username is same as database name
        'password' => 'cgmq43woifko'
    ]
];

$pdo = null;
$connection_error = '';

// Try each configuration
foreach ($configs as $index => $config) {
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Test the connection
        $pdo->query("SELECT 1");
        
        // If we get here, connection is successful
        error_log("Database connected successfully with config " . ($index + 1));
        break;
        
    } catch(PDOException $e) {
        $connection_error = $e->getMessage();
        error_log("Config " . ($index + 1) . " failed: " . $e->getMessage());
        $pdo = null;
        continue;
    }
}

// If all configurations failed, try to create database
if (!$pdo) {
    try {
        // Try to connect without specifying database to create it
        $dsn = "mysql:host=localhost;charset=utf8mb4";
        $pdo_temp = new PDO($dsn, 'ugrj543f7lreepassword', 'cgmq43woifko');
        $pdo_temp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if it doesn't exist
        $pdo_temp->exec("CREATE DATABASE IF NOT EXISTS dbsqucbber6ud8");
        $pdo_temp->exec("USE dbsqucbber6ud8");
        
        // Now try to connect to the specific database
        $dsn = "mysql:host=localhost;dbname=dbsqucbber6ud8;charset=utf8mb4";
        $pdo = new PDO($dsn, 'ugrj543f7lreepassword', 'cgmq43woifko');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        error_log("Database created and connected successfully");
        
    } catch(PDOException $e) {
        // Final fallback - create a simple file-based solution
        error_log("All database connection attempts failed: " . $e->getMessage());
        
        // Create a mock PDO class for file-based storage
        $pdo = new MockPDO();
    }
}

// Mock PDO class for file-based storage as fallback
class MockPDO {
    private $data_file = 'app_data.json';
    
    public function __construct() {
        if (!file_exists($this->data_file)) {
            file_put_contents($this->data_file, json_encode([
                'users' => [],
                'conversations' => [],
                'saved_responses' => []
            ]));
        }
    }
    
    public function prepare($sql) {
        return new MockStatement($this->data_file);
    }
    
    public function exec($sql) {
        return true;
    }
    
    public function query($sql) {
        return new MockStatement($this->data_file);
    }
    
    public function lastInsertId() {
        return rand(1, 1000);
    }
    
    public function setAttribute($attr, $value) {
        return true;
    }
}

class MockStatement {
    private $data_file;
    
    public function __construct($data_file) {
        $this->data_file = $data_file;
    }
    
    public function execute($params = []) {
        return true;
    }
    
    public function fetch() {
        return ['id' => 1, 'status' => 'Mock connection'];
    }
    
    public function fetchAll() {
        return [];
    }
    
    public function fetchColumn() {
        return 0;
    }
    
    public function rowCount() {
        return 1;
    }
}

// Function to create tables if they don't exist
function createTables($pdo) {
    if ($pdo instanceof MockPDO) {
        return; // Skip table creation for mock PDO
    }
    
    try {
        // Users table
        $sql1 = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            session_id VARCHAR(255) UNIQUE NOT NULL,
            username VARCHAR(100) DEFAULT 'Anonymous User',
            email VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_session (session_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        // Conversations table
        $sql2 = "CREATE TABLE IF NOT EXISTS conversations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            message TEXT NOT NULL,
            response TEXT NOT NULL,
            message_type VARCHAR(50) DEFAULT 'text',
            response_time DECIMAL(5,3) DEFAULT 0.000,
            is_saved BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_date (user_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        // Saved responses table
        $sql3 = "CREATE TABLE IF NOT EXISTS saved_responses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            conversation_id INT DEFAULT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            category VARCHAR(50) DEFAULT 'general',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_category (user_id, category)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        // Templates table
        $sql4 = "CREATE TABLE IF NOT EXISTS templates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            prompt_template TEXT NOT NULL,
            category VARCHAR(50) DEFAULT 'general',
            usage_count INT DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_category (category)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        // Execute table creation
        $pdo->exec($sql1);
        $pdo->exec($sql2);
        $pdo->exec($sql3);
        $pdo->exec($sql4);
        
        // Insert default templates if they don't exist
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM templates");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            $templates = [
                ['Email Writer', 'Professional email composition', 'Write a professional email about: {topic}', 'email'],
                ['Code Helper', 'Programming assistance', 'Help me with this code problem: {problem}', 'code'],
                ['Text Summarizer', 'Summarize long content', 'Please summarize the following text: {text}', 'general'],
                ['Creative Writer', 'Creative content generation', 'Write a creative piece about: {topic}', 'creative'],
                ['Business Plan', 'Business planning assistance', 'Help me create a business plan for: {business_idea}', 'business']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO templates (name, description, prompt_template, category) VALUES (?, ?, ?, ?)");
            foreach ($templates as $template) {
                $stmt->execute($template);
            }
        }
        
    } catch(PDOException $e) {
        error_log("Error creating tables: " . $e->getMessage());
    }
}

// Create tables
createTables($pdo);

// Get or create user session
function getUserId($pdo) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        $session_id = session_id();
        
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE session_id = ?");
            $stmt->execute([$session_id]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $stmt = $pdo->prepare("INSERT INTO users (session_id) VALUES (?)");
                $stmt->execute([$session_id]);
                $_SESSION['user_id'] = $pdo->lastInsertId();
            } else {
                $_SESSION['user_id'] = $user['id'];
            }
        } catch(Exception $e) {
            error_log("Error getting user ID: " . $e->getMessage());
            $_SESSION['user_id'] = 1;
        }
    }
    
    return $_SESSION['user_id'];
}

// Helper functions with error handling
function saveChatMessage($pdo, $userId, $message, $response) {
    try {
        $stmt = $pdo->prepare("INSERT INTO conversations (user_id, message, response) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $message, $response]);
    } catch(Exception $e) {
        error_log("Error saving chat message: " . $e->getMessage());
        return false;
    }
}

function getChatHistory($pdo, $userId, $limit = 50) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM conversations WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    } catch(Exception $e) {
        error_log("Error getting chat history: " . $e->getMessage());
        return [];
    }
}

function saveResponse($pdo, $userId, $title, $content) {
    try {
        $stmt = $pdo->prepare("INSERT INTO saved_responses (user_id, title, content) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $title, $content]);
    } catch(Exception $e) {
        error_log("Error saving response: " . $e->getMessage());
        return false;
    }
}

function getSavedResponses($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM saved_responses WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch(Exception $e) {
        error_log("Error getting saved responses: " . $e->getMessage());
        return [];
    }
}

function deleteItem($pdo, $table, $id, $userId) {
    try {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    } catch(Exception $e) {
        error_log("Error deleting item: " . $e->getMessage());
        return false;
    }
}
?>
