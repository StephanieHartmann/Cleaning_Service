<?php
require_once '../src/db.php';


if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$order_id = $_GET['id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee = $_POST['assigned_to'];
    $date     = $_POST['service_date'];
    $time     = $_POST['service_time'];
    
    // cleaning duration 
    $duration_hours = 3; 

    $sql_check = "SELECT order_id, service_time FROM ServiceOrder 
                  WHERE assigned_to = :employee 
                  AND service_date = :date 
                  AND order_id != :current_id 
                  AND status IN ('Scheduled', 'Invoiced')";

    $stmt = $pdo->prepare($sql_check);
    $stmt->execute([
        ':employee'   => $employee,
        ':date'       => $date,
        ':current_id' => $order_id
    ]);
    
    $existing_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
  
    $new_start = strtotime($time);
    $new_end   = strtotime("+$duration_hours hours", $new_start);
    
    $has_conflict = false;


    foreach ($existing_jobs as $job) {
        $existing_start = strtotime($job['service_time']);
        $existing_end   = strtotime("+$duration_hours hours", $existing_start);


        if ($new_start < $existing_end && $new_end > $existing_start) {
            $has_conflict = true;
            $error = "Conflict! $employee is already busy at this time (Order #" . $job['order_id'] . ")";
            break; 
        }
    }

    if (!$has_conflict) {
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

        header('Location: index.php'); 
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM ServiceOrder WHERE order_id = :id");
$stmt->execute([':id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Order #<?= $order['order_id'] ?></title>
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* CSS Básico só para esta página */
        .container { max-width: 500px; margin: 40px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-confirm { background-color: #27ae60; color: white; border: none; padding: 12px; cursor: pointer; border-radius: 4px; margin-top: 20px; width: 100%; font-size: 16px; font-weight: bold; }
        .btn-confirm:hover { background-color: #219150; }
        .btn-cancel { background-color: #95a5a6; color: white; text-decoration: none; padding: 10px; border-radius: 4px; display: block; text-align: center; margin-top: 10px; }
        .error { background: #e74c3c; color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center; }
        .info-box { background: #f9f9f9; padding: 15px; border-radius: 4px; border: 1px solid #ddd; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align:center">Schedule Order #<?= $order['order_id'] ?></h2>

    <?php if ($error): ?>
        <div class="error">⚠️ <?= $error ?></div>
    <?php endif; ?>

    <div class="info-box">
        <p><strong>Customer:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
        <p><strong>Service:</strong> <?= htmlspecialchars($order['cleaning_type']) ?></p>
    </div>

    <form method="POST">
        <label>Select Employee:</label>
        <select name="assigned_to" required>
            <option value="">-- Choose Employee --</option>
            <option value="Hans Müller">Hans Müller</option>
            <option value="Petra Schmidt">Petra Schmidt</option>
            <option value="Klaus Wagner">Klaus Wagner</option>
            <option value="Heidi Weber">Heidi Weber</option>
        </select>

        <label>Date:</label>
        <input type="date" name="service_date" required min="<?= date('Y-m-d') ?>">

        <label>Start Time:</label>
        <input type="time" name="service_time" required>

        <button type="submit" class="btn-confirm">Confirm Schedule</button>
        <a href="index.php" class="btn-cancel">Cancel</a>
    </form>
</div>

</body>
</html>