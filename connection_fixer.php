<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Database Connection Fixer</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    .box { border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 5px; }
</style>";

// Step 1: Check if MySQL extension is loaded
echo "<div class='box'>";
echo "<h2>Step 1: Checking PHP MySQL Support</h2>";
if (extension_loaded('pdo_mysql')) {
    echo "<span class='success'>✅ PDO MySQL extension is loaded</span><br>";
} else {
    echo "<span class='error'>❌ PDO MySQL extension is NOT loaded</span><br>";
    echo "<p>Contact your hosting provider to enable PDO MySQL extension.</p>";
}
echo "</div>";

// Step 2: Test basic connection without database
echo "<div class='box'>";
echo "<h2>Step 2: Testing Basic MySQL Connection (without database)</h2>";

$host = 'localhost';
$username = 'ugrj543f7lreepassword';
$password = 'cgmq43woifko';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<span class='success'>✅ Basic MySQL connection successful!</span><br>";
    
    // Get MySQL version
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "<span class='info'>MySQL Version: " . $result['version'] . "</span><br>";
    
    // List all databases user has access to
    try {
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<span class='info'>Databases you have access to:</span><br>";
        foreach ($databases as $db) {
            echo "- $db<br>";
            if ($db === 'dbsqucbber6ud8') {
                echo "<span class='success'>✅ Target database 'dbsqucbber6ud8' found!</span><br>";
            }
        }
    } catch (Exception $e) {
        echo "<span class='error'>Could not list databases: " . $e->getMessage() . "</span><br>";
    }
    
} catch (PDOException $e) {
    echo "<span class='error'>❌ Basic connection failed: " . $e->getMessage() . "</span><br>";
    echo "<p><strong>This means your credentials are wrong. Please check:</strong></p>";
    echo "<ul>";
    echo "<li>Username: Is it exactly 'ugrj543f7lreepassword'?</li>";
    echo "<li>Password: Is it exactly 'cgmq43woifko'?</li>";
    echo "<li>Host: Should it be 'localhost' or something else?</li>";
    echo "</ul>";
}
echo "</div>";

// Step 3: Try to create database if basic connection works
if (isset($pdo)) {
    echo "<div class='box'>";
    echo "<h2>Step 3: Creating Database (if needed)</h2>";
    
    try {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS dbsqucbber6ud8");
        echo "<span class='success'>✅ Database 'dbsqucbber6ud8' created/verified</span><br>";
    } catch (PDOException $e) {
        echo "<span class='error'>❌ Could not create database: " . $e->getMessage() . "</span><br>";
    }
    echo "</div>";
}

// Step 4: Test connection to specific database
echo "<div class='box'>";
echo "<h2>Step 4: Testing Connection to Specific Database</h2>";

try {
    $pdo_db = new PDO("mysql:host=$host;dbname=dbsqucbber6ud8", $username, $password);
    $pdo_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<span class='success'>✅ Connection to 'dbsqucbber6ud8' successful!</span><br>";
    
    // Test creating a simple table
    try {
        $pdo_db->exec("CREATE TABLE IF NOT EXISTS test_table (id INT AUTO_INCREMENT PRIMARY KEY, test_data VARCHAR(100))");
        echo "<span class='success'>✅ Can create tables in database</span><br>";
        
        // Test inserting data
        $stmt = $pdo_db->prepare("INSERT INTO test_table (test_data) VALUES (?)");
        $stmt->execute(['Connection test successful']);
        echo "<span class='success'>✅ Can insert data into database</span><br>";
        
        // Clean up test table
        $pdo_db->exec("DROP TABLE test_table");
        echo "<span class='info'>Test table cleaned up</span><br>";
        
    } catch (PDOException $e) {
        echo "<span class='error'>❌ Database operations failed: " . $e->getMessage() . "</span><br>";
    }
    
} catch (PDOException $e) {
    echo "<span class='error'>❌ Connection to database failed: " . $e->getMessage() . "</span><br>";
}
echo "</div>";

// Step 5: Generate working db.php file
if (isset($pdo_db)) {
    echo "<div class='box'>";
    echo "<h2>Step 5: Generate Working db.php File</h2>";
    echo "<span class='success'>✅ All tests passed! Generating working db.php file...</span><br>";
    
    $db_content = '<?php
// Working database configuration - Generated automatically
$host = \'localhost\';
$dbname = \'dbsqucbber6ud8\';
$username = \'ugrj543f7lreepassword\';
$password = \'cgmq43woifko\';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get or create user session
function getUserId($pdo) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION[\'user_id\'])) {
        $session_id = session_id();
        
        try {
            // Create users table if not exists
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(255) UNIQUE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            $stmt = $pdo->prepare("SELECT id FROM users WHERE session_id = ?");
            $stmt->execute([$session_id]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $stmt = $pdo->prepare("INSERT INTO users (session_id) VALUES (?)");
                $stmt->execute([$session_id]);
                $_SESSION[\'user_id\'] = $pdo->lastInsertId();
            } else {
                $_SESSION[\'user_id\'] = $user[\'id\'];
            }
        } catch(PDOException $e) {
            error_log("Error getting user ID: " . $e->getMessage());
            $_SESSION[\'user_id\'] = 1;
        }
    }
    
    return $_SESSION[\'user_id\'];
}

// Create all necessary tables
function createTables($pdo) {
    try {
        // Users table
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            session_id VARCHAR(255) UNIQUE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Conversations table
        $pdo->exec("CREATE TABLE IF NOT EXISTS conversations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            message TEXT NOT NULL,
            response TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Saved responses table
        $pdo->exec("CREATE TABLE IF NOT EXISTS saved_responses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
    } catch(PDOException $e) {
        error_log("Error creating tables: " . $e->getMessage());
    }
}

// Create tables
createTables($pdo);
?>';

    if (file_put_contents('db_working.php', $db_content)) {
        echo "<span class='success'>✅ Working db.php file created as 'db_working.php'</span><br>";
        echo "<p><strong>Next steps:</strong></p>";
        echo "<ol>";
        echo "<li>Rename 'db_working.php' to 'db.php'</li>";
        echo "<li>Update your other PHP files to use this working database connection</li>";
        echo "<li>Test your application</li>";
        echo "</ol>";
    } else {
        echo "<span class='error'>❌ Could not create db.php file. Copy the code manually.</span><br>";
    }
    echo "</div>";
}

// Alternative solutions if main connection fails
if (!isset($pdo_db)) {
    echo "<div class='box'>";
    echo "<h2>🔄 Alternative Solutions</h2>";
    echo "<p><strong>Since the database connection failed, here are alternative approaches:</strong></p>";
    
    echo "<h3>Option 1: Contact Your Hosting Provider</h3>";
    echo "<p>Ask them for:</p>";
    echo "<ul>";
    echo "<li>Correct database host (might not be 'localhost')</li>";
    echo "<li>Correct username and password</li>";
    echo "<li>Whether the database 'dbsqucbber6ud8' exists</li>";
    echo "<li>User permissions for this database</li>";
    echo "</ul>";
    
    echo "<h3>Option 2: Check cPanel/Database Manager</h3>";
    echo "<p>If you have cPanel access:</p>";
    echo "<ul>";
    echo "<li>Go to MySQL Databases</li>";
    echo "<li>Check if database 'dbsqucbber6ud8' exists</li>";
    echo "<li>Check if user 'ugrj543f7lreepassword' exists and has access</li>";
    echo "<li>Reset password if needed</li>";
    echo "</ul>";
    
    echo "<h3>Option 3: Use SQLite (File-based Database)</h3>";
    echo "<p>If MySQL continues to fail, I can provide a SQLite version that doesn't require MySQL server.</p>";
    
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>If you're still having issues, please share the output of this page so I can help you further.</strong></p>";
?>
