<?php
require_once '../../src/db.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // -------------------------------------------------------------
    // GET: Retrieve list of employees from Database
    // -------------------------------------------------------------
    try {
        // Select only names, ordered alphabetically
        $stmt = $pdo->query("SELECT name FROM Employee ORDER BY name ASC");
        
        // Fetch specific column as a simple array (['Hans', 'Petra'])
        $employees = $stmt->fetchAll(PDO::FETCH_COLUMN); 
        
        echo json_encode($employees);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
