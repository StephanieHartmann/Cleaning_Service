<?php
require_once '../../src/db.php';

header('Content-Type: application/json');

// Check if ID is provided for operations requiring it
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID missing']);
    exit;
}

$order_id = $_GET['id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // -------------------------------------------------------------
    // GET: Retrieve a single order by ID
    // -------------------------------------------------------------
    try {
        $stmt = $pdo->prepare("SELECT * FROM ServiceOrder WHERE order_id = :id");
        $stmt->execute([':id' => $order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            exit;
        }

        echo json_encode($order);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }

} elseif ($method === 'POST') {
    // -------------------------------------------------------------
    // POST: Perform actions on an existing order (Schedule, Complete, Delete)
    // -------------------------------------------------------------
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    try {
        // --- ACTION: DELETE ---
        if ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM ServiceOrder WHERE order_id = :id");
            $stmt->execute([':id' => $order_id]);
            echo json_encode(['message' => 'Order deleted']);

        // --- ACTION: CANCEL ---
        } elseif ($action === 'cancel_order') {
            $sql = "UPDATE ServiceOrder 
                    SET status = 'Cancelled', assigned_to = NULL, service_date = NULL, service_time = NULL 
                    WHERE order_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $order_id]);
            echo json_encode(['message' => 'Order cancelled']);

        // --- ACTION: COMPLETE (Add Report & Hours) ---
        } elseif ($action === 'complete_order') {
            $hours = $input['hours_worked'] ?? null;
            $report = $input['report_text'] ?? null;

            $sql = "UPDATE ServiceOrder 
                    SET status = 'Completed', 
                        hours_worked = :hours, 
                        report_text = :report 
                    WHERE order_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':hours' => $hours,
                ':report' => $report,
                ':id' => $order_id
            ]);
            echo json_encode(['message' => 'Order completed']);

        // --- ACTION: INVOICE ---
        } elseif ($action === 'invoice_order') {
            $sql = "UPDATE ServiceOrder SET status = 'Invoiced' WHERE order_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $order_id]);
            echo json_encode(['message' => 'Order invoiced']);

        // --- ACTION: SCHEDULE (Assign Employee & Time) ---
        } elseif ($action === 'schedule') {
            $employee = $input['assigned_to'];
            $date     = $input['service_date'];
            $time     = $input['service_time'];
            $duration_hours = 3; // Default job duration

            // Conflict Check: Check if employee is already busy at this time
             $stmt = $pdo->prepare("SELECT order_id, service_time FROM ServiceOrder 
                   WHERE assigned_to = :employee 
                   AND service_date = :date 
                   AND order_id != :current_id 
                   AND status IN ('Scheduled', 'Invoiced')");

            $stmt->execute([
                ':employee'   => $employee,
                ':date'       => $date,
                ':current_id' => $order_id
            ]);
            
            $existing_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $new_start = strtotime($time);
            $new_end   = strtotime("+$duration_hours hours", $new_start);
            
            // Loop through existing jobs to check for time overlap
            foreach ($existing_jobs as $job) {
                $existing_start = strtotime($job['service_time']);
                $existing_end   = strtotime("+$duration_hours hours", $existing_start);
                if ($new_start < $existing_end && $new_end > $existing_start) {
                    throw new Exception("Conflict! $employee is busy at this time.");
                }
            }

            $sql = "UPDATE ServiceOrder 
                    SET assigned_to = :employee, 
                        service_date = :date, 
                        service_time = :time, 
                        status = 'Scheduled'
                    WHERE order_id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':employee' => $employee,
                ':date'     => $date,
                ':time'     => $time,
                ':id'       => $order_id
            ]);
            echo json_encode(['message' => 'Order scheduled']);

        } else {
             http_response_code(400);
             echo json_encode(['error' => 'Invalid action']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
