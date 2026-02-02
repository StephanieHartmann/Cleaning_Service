<?php
require_once __DIR__ . '/../src/db.php';

echo "--- DB DEBUG START ---\n";
try {
    $stmt = $pdo->query("SELECT * FROM Employee");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        echo "Table Employee is EMPTY.\n";
    } else {
        foreach ($rows as $r) {
            echo "ID: " . $r['employee_id'] . " | Name: " . $r['name'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
echo "--- DB DEBUG END ---\n";
?>
