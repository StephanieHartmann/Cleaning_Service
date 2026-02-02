<?php
require_once '../src/db.php';
header('Content-Type: text/html; charset=utf-8');

try {
    echo "<h1>Updating Employees...</h1>";
    
    // 1. Delete existing (Reset)
    $stmt = $pdo->exec("DELETE FROM Employee");
    echo "<p>Deleted old employee records.</p>";

    // 2. Insert  Names
    $german_names = ['Hans Müller', 'Petra Schmidt', 'Klaus Weber'];
    $stmt = $pdo->prepare("INSERT INTO Employee (name) VALUES (:name)");

    foreach ($german_names as $name) {
        $stmt->execute([':name' => $name]);
        echo "<p>Inserted: $name</p>";
    }

    echo "<h2>Success! Database updated.</h2>";
    echo "<p><a href='check_db.php'>Click here to verify</a></p>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
