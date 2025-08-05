<?php
echo "<h2>🔍 Database Connection Debugger</h2>";

// Test different connection scenarios
$tests = [
    [
        'name' => 'Original Credentials',
        'host' => 'localhost',
        'dbname' => 'dbsqucbber6ud8',
        'username' => 'ugrj543f7lreepassword',
        'password' => 'cgmq43woifko'
    ],
    [
        'name' => 'Alternative Host (127.0.0.1)',
        'host' => '127.0.0.1',
        'dbname' => 'dbsqucbber6ud8',
        'username' => 'ugrj543f7lreepassword',
        'password' => 'cgmq43woifko'
    ],
    [
        'name' => 'Username as Database Name',
        'host' => 'localhost',
        'dbname' => 'dbsqucbber6ud8',
        'username' => 'dbsqucbber6ud8',
        'password' => 'cgmq43woifko'
    ],
    [
        'name' => 'Without Database (to create it)',
        'host' => 'localhost',
        'dbname' => '',
        'username' => 'ugrj543f7lreepassword',
        'password' => 'cgmq43woifko'
    ]
];

foreach ($tests as $index => $test) {
    echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px;'>";
    echo "<h3>Test " . ($index + 1) . ": " . $test['name'] . "</h3>";
    echo "<strong>Details:</strong><br>";
    echo "Host: " . $test['host'] . "<br>";
    echo "Database: " . ($test['dbname'] ?: 'None (for creation)') . "<br>";
    echo "Username: " . $test['username'] . "<br>";
    echo "Password: " . str_repeat('*', strlen($test['password'])) . "<br><br>";
    
    try {
        if ($test['dbname']) {
            $dsn = "mysql:host={$test['host']};dbname={$test['dbname']};charset=utf8mb4";
        } else {
            $dsn = "mysql:host={$test['host']};charset=utf8mb4";
        }
        
        $pdo = new PDO($dsn, $test['username'], $test['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ <span style='color: green; font-weight: bold;'>CONNECTION SUCCESSFUL!</span><br>";
        
        // Get MySQL version
        $stmt = $pdo->query("SELECT VERSION() as version");
        $result = $stmt->fetch();
        echo "MySQL Version: " . $result['version'] . "<br>";
        
        // If no database was specified, try to create it
        if (!$test['dbname']) {
            try {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS dbsqucbber6ud8");
                echo "✅ Database 'dbsqucbber6ud8' created/verified<br>";
                
                $pdo->exec("USE dbsqucbber6ud8");
                echo "✅ Successfully switched to database<br>";
            } catch(PDOException $e) {
                echo "❌ Could not create/use database: " . $e->getMessage() . "<br>";
            }
        } else {
            // Show tables in database
            try {
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll();
                echo "Tables in database: " . count($tables) . "<br>";
            } catch(PDOException $e) {
                echo "Could not list tables: " . $e->getMessage() . "<br>";
            }
        }
        
        echo "<span style='color: green; font-weight: bold;'>✅ THIS CONFIGURATION WORKS!</span>";
        
    } catch(PDOException $e) {
        echo "❌ <span style='color: red; font-weight: bold;'>CONNECTION FAILED</span><br>";
        echo "Error: " . $e->getMessage() . "<br>";
        echo "Error Code: " . $e->getCode() . "<br>";
    }
    
    echo "</div>";
}

echo "<hr>";
echo "<h3>📋 Troubleshooting Checklist:</h3>";
echo "<ul>";
echo "<li>✓ Check if MySQL server is running</li>";
echo "<li>✓ Verify database 'dbsqucbber6ud8' exists</li>";
echo "<li>✓ Confirm user 'ugrj543f7lreepassword' has proper permissions</li>";
echo "<li>✓ Test if user can connect from 'localhost' vs '127.0.0.1'</li>";
echo "<li>✓ Check hosting provider's specific connection requirements</li>";
echo "<li>✓ Verify password is exactly 'cgmq43woifko'</li>";
echo "</ul>";

echo "<h3>🔧 Next Steps:</h3>";
echo "<p>If one of the tests above shows '✅ THIS CONFIGURATION WORKS!', use that configuration in your db.php file.</p>";
echo "<p>If all tests fail, contact your hosting provider for the correct database connection details.</p>";
?>
