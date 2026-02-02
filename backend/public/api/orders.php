<?php
require_once '../../src/db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // -------------------------------------------------------------
    // GET: List all orders (optionally filtered by status)
    // -------------------------------------------------------------
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
    
    try {
        if ($status_filter) {
            // Filter by specific status (e.g. New, Scheduled)
            $sql = "SELECT * FROM ServiceOrder WHERE status = :status ORDER BY order_id DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':status' => $status_filter]);
        } else {
            // Return all orders (Default)
            $stmt = $pdo->query("SELECT * FROM ServiceOrder ORDER BY order_id DESC");
        }
        $orders = $stmt->fetchAll();
        echo json_encode($orders);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }

} elseif ($method === 'POST') {
    // -------------------------------------------------------------
    // POST: Create a new Service Order
    // -------------------------------------------------------------
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        exit;
    }

    try {
        // Validation: Check required fields
        if(empty($input['first_name']) || empty($input['last_name']) || 
           empty($input['phone']) || empty($input['email']) || 
           empty($input['street']) || empty($input['number']) || 
           empty($input['zip']) || empty($input['city']) || 
           empty($input['size_sqm'])) {
            
            throw new Exception("Please fill in all required fields.");
        }

        // Concatenate address for storage
        $full_address = $input['street'] . ' ' . $input['number'] . ', ' . $input['zip'] . ' ' . $input['city'];

        // Insert into Database
        $sql = "INSERT INTO ServiceOrder (first_name, last_name, phone, email, address, cleaning_type, size_sqm)
                VALUES (:fname, :lname, :phone, :email, :address, :type, :size)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':fname' => $input['first_name'],
            ':lname' => $input['last_name'],
            ':phone' => $input['phone'],
            ':email' => $input['email'],
            ':address' => $full_address,
            ':type' => $input['cleaning_type'],
            ':size' => $input['size_sqm'],
        ]);

        http_response_code(201);
        echo json_encode(['message' => 'Order created successfully', 'id' => $pdo->lastInsertId()]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
