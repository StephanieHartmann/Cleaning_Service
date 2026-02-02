<?php
require_once '../src/db.php';
header('Content-Type: text/html; charset=utf-8');

try {
    // Fetch Employees
    echo "<h1>Supabase Database Check</h1>";
    echo "<h2>Employees</h2>";
    $stmt = $pdo->query("SELECT * FROM Employee");
    $employees = $stmt->fetchAll();
    
    if (count($employees) > 0) {
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Name</th></tr>";
        foreach ($employees as $emp) {
            echo "<tr><td>{$emp['employee_id']}</td><td>{$emp['name']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No employees found.</p>";
    }

    // Fetch Orders
    echo "<h2>Recent Service Orders</h2>";
    $stmt2 = $pdo->query("SELECT order_id, customer_name, status, assigned_to FROM ServiceOrder ORDER BY order_id DESC LIMIT 5");
    $orders = $stmt2->fetchAll();

    if (count($orders) > 0) {
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Customer</th><th>Status</th><th>Assigned To</th></tr>";
        foreach ($orders as $ord) {
            echo "<tr><td>{$ord['order_id']}</td><td>{$ord['customer_name']}</td><td>{$ord['status']}</td><td>{$ord['assigned_to']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No orders found.</p>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
