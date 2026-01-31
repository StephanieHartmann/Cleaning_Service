<?php
require_once '../src/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $id     = $_POST['order_id'];

    if ($action === 'delete') {
        // PERMANENT DELETE (For registration errors)
        $stmt = $pdo->prepare("DELETE FROM ServiceOrder WHERE order_id = :id");
        $stmt->execute([':id' => $id]);
        
        header('Location: index.php?msg=deleted');
        exit;

    } elseif ($action === 'cancel_order') {
        // CLIENT CANCELED (Keep in history as Cancelled)
        // We removed the appointment, but we kept the order with a 'Cancelled' status.
        $sql = "UPDATE ServiceOrder 
                SET status = 'Cancelled', assigned_to = NULL, service_date = NULL, service_time = NULL 
                WHERE order_id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        header("Location: view.php?id=$id");
        exit;

    } elseif ($action === 'reset_schedule') {
        // reschedule
        $sql = "UPDATE ServiceOrder 
                SET status = 'New', assigned_to = NULL, service_date = NULL, service_time = NULL 
                WHERE order_id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        header("Location: view.php?id=$id");
        exit;

    } elseif ($action === 'complete_order') {
        // Mark as Done
        // change status for Completed
        $sql = "UPDATE ServiceOrder 
                SET status = 'Completed' 
                WHERE order_id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        header("Location: view.php?id=$id");
        exit;
    }
}

header('Location: index.php');
exit;