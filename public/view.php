<?php
require_once '../src/db.php';

if (!isset($_GET['id'])) {
    die("Order ID is missing.");
}
$order_id = $_GET['id'];

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = $_POST['action'];

    try {
        if ($action === 'schedule') {

            $sql = "UPDATE ServiceOrder SET employee_id = :emp, scheduled_at = :date, status = 'Scheduled' WHERE order_id = :id";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':emp' => $_POST['employee_id'], 
                ':date' => $_POST['scheduled_at'], 
                ':id' => $order_id
            ]);

            $message = "Order successfully scheduled!";
            
        } elseif ($action === 'complete') {

        $sql = "UPDATE ServiceOrder SET report_text = :report, hours_worked = :hours, status= 'Done' WHERE order_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':report' => $_POST['report_text'], 
                ':hours' => $_POST['hours_worked'], 
                ':id' => $order_id
            ]);

        $message = "Order marked as done!";

        } elseif ($action === 'invoice') {

        $sql = "UPDATE ServiceOrder SET status = 'Invoiced' WHERE order_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $order_id]);
        $message = "Order marked as invoiced!";
        }
    
    } catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM ServiceOrder WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        die("Order not found!");
    }

   $stmtEmp = $pdo->query("SELECT * FROM Employee");
   $employees = $stmtEmp->fetchAll();

    $assigned_employee = "Not assigned";
    if ($order['employee_id']) {
        foreach($employees as $emp) {
            if ($emp['employee_id'] == $order['employee_id']) {
                $assigned_employee = $emp['name'];
                break;
            }
        }
    }
} catch (Exception $e) {
    die("Database error (Fix db.php first!).");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <style>
        body {
            font-family: sans-serif;
            background-color:#FAF8F5;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        h1, h2 {
            color: #4D403A;
        }

        .section {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            color: #666;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #4D403A;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-print {
            background-color: #6c757d;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .alert {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php"
    style="color: #666; text-decoration: none;">&larr; Back to List</a>

    <h1>Service Order #<?= $order['order_id'] ?></h1>

    <?php if ($message): ?>
        <div class="alert"><?=  $message  ?></div>
    <?php endif; ?>

    <div class="section">
        <h2>Customer Information</h2>
    <div class="info-row"><span class="label">Name:</span> <span><?= htmlspecialchars($order['customer_name']) ?></span></div>

    <div class="info-row"><span class="label">Phone:</span> <span><?= htmlspecialchars($order['Phone']) ?></span></div>

    <div class="info-row"><span class="label">Email:</span> <span><?= htmlspecialchars($order['Email']) ?></span></div>

    <div class="info-row"><span class="label">Address:</span> <span><?= htmlspecialchars($order['Address']) ?></span></div>

    </div>

    <div class="section">
        <h2>Service Details</h2>

        <div class="info-row"><span class="label">Type:</span> <span><?= htmlspecialchars($order['cleaning_type']) ?></span></div>

        <div class="info-row"><span class="label">Size:</span> <span><?= htmlspecialchars($order['size_sqm']) ?></span></div>

        <div class="info-row"><span class="label">Status:</span> <span><?= htmlspecialchars($order['status']) ?></span></div>

        <?php if ($order['status'] !== 'New'): ?>

             <div class="info-row"><span class="label">Employee:</span> <span><?= htmlspecialchars($assigned_employee) ?></span></div>
            
              <div class="info-row"><span class="label">Date:</span> <span><?= $order ['schedulled_at'] ?></span></div>
            <?php endif; ?>
    </div>

    <div class="actions">

        <?php if ($order['status'] === 'New'): ?>
            <h3>Action: Schedule Order</h3>
            <form method="POST">
                <input type="hidden" name="action" value="schedule">

                <label>Assign Employee:</label>
                <select name="employeed_id" required>
                    <option value="">Select an employee...</option>
                    <?php foreach ($employess as $emp): ?>

                        <option value="<?= $emp['employee_id'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
                        
                        <?php endforeach; ?>
                </select>

                <label>Schedule Data & Time:</label>
                <input type="datetime-local" name="scheduled_at" required>

                <button type="submit" class="btn btn-primary">Schedule Order</button>
            </form> 
        <?php elseif ($order['status'] === 'Scheduled'): ?>
            <h3>Action: Work Report</h3>
            <form method="POST">
                <input type="hidden" name="action" value="complete">

                <label>Hours Worked:</label>
                <input type="number" step="0.5" name="hours_worked" required placeholder="e.g. 4.5">

                <label>Report / Notes:</label>
                <textarea name="report_text" rows="4" required placeholder="What was cleaned?"></textarea>

                <button type="submit" class="btn-primary">Mark as Done</button>
            </form>

            <?php elseif ($order['status'] === 'Done' || $order['status'] === 'Invoiced'): ?>

                <h3>Work Report</h3>
                <p><strong>Hours:0</strong> <?= $order['hours_worked'] ?></p>
                <p style="background: #f9f9f9;
                padding: 10px; border-left: 3px solid #ccc;">
                <?= nl2br(htmlspecialchars($order['report_text'])) ?>
            </p>

            <div style="margin-top: 20px">
                <?php if ($order['status'] === 'Done'): ?>
                    <form method="POST" style="display:inline;"
                    <input type="hidden" name="action" value="invoice">

                    <button type="submit" class= "btn btn-success">Mark as Invoiced</button>

                </form>
            <?php endif; ?>

            <a href="print.php?id=id<?= $order_id ?>" target="_blank" class="btn btn-print">Print Order</a>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>