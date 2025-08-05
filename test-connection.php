<?php
$host = 'localhost';
$dbname = 'dbsqucbber6ud8';
$username = 'ugrj543f7lreepassword';
$password = 'cgmq43woifko';

echo "<h2>Testing Database Connection</h2>";
echo "Host: $host<br>";
echo "Database: $dbname<br>";
echo "Username: $username<br>";
echo "Password: " . str_repeat('*', strlen($password)) . "<br><br>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ <strong>Connection successful!</strong><br>";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "MySQL Version: " . $result['version'] . "<br>";
    
    // Test database access
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "Tables in database: " . count($tables) . "<br>";
    
    echo "<br>✅ <strong>Database connection is working perfectly!</strong>";
    
} catch(PDOException $e) {
    echo "❌ <strong>Connection failed:</strong> " . $e->getMessage() . "<br>";
    echo "Error Code: " . $e->getCode() . "<br>";
    
    // Additional debugging info
    echo "<br><strong>Debugging Info:</strong><br>";
    echo "- Make sure the database 'dbsqucbber6ud8' exists<br>";
    echo "- Make sure the user 'ugrj543f7lreepassword' has access to this database<br>";
    echo "- Check if MySQL server is running<br>";
    echo "- Verify the host is 'localhost' (some hosts use different addresses)<br>";
}
?>
