<?php
require_once '../src/db.php';

if (!isset($_GET['id'])) {
    die("Order ID missing.");
}
$order_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM ServiceOrder WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) die("Order not found.");

    
    $order['employee_name'] = 'Not Assigned'; 
    if (!empty($order['employee_id'])) {
        $stmtEmp = $pdo->prepare("SELECT name FROM Employee WHERE employee_id = ?");
        $stmtEmp->execute([$order['employee_id']]);
        $emp = $stmtEmp->fetch();
        if ($emp) {
            $order['employee_name'] = $emp['name'];
        }
    }
} catch (Exception $e) {
    die("Erro: " . $e->getMessage()); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Order #<?= $order_id ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .header {
            border-bottom: 2px solid #4D403A;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }

        .company-info {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            background: #fff;
        }

        .label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }

        .row { margin-bottom: 8px; }

        @media print {
            .no-print { display: none; }
        }

    </style>
</head>
<body onload="window.print()"><div class="container">
    <div class="header">
        <h1>Cleaning Service - Service Order</h1>
        <div class="company-info">
            Cleaning Service AG<br>
            Main Street 10, 8000 Zurich<br>
            Phone: 044 123 45 67 | Email: info@cleaningservice.ch
        </div>
    </div>

    <h3>Customer Information</h3>
    <div class="box">
        <div class="row"><span class="label">Name:</span> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></div>

        <div class="row"><span class="label">Address:</span> <?= htmlspecialchars($order['address']) ?></div>

        <div class="row"><span class="label">Phone:</span> <?= htmlspecialchars($order['phone']) ?></div>

        <div class="row"><span class="label">Email:</span> <?= htmlspecialchars($order['email']) ?></div>

    </div>

     <h3>Service Details</h3>
     <div class="box">
        <div class="row"><span class="label">Order ID:</span> #<?= $order['order_id'] ?></div>

        <div class="row"><span class="label">Status:</span> <?= htmlspecialchars($order['status']) ?></div>

        <div class="row"><span class="label">Type:</span> <?= htmlspecialchars($order['cleaning_type']) ?></div>

        <div class="row"><span class="label">Size:</span> <?= htmlspecialchars($order['size_sqm']) ?> sqm</div>

        <div class="row"><span class="label">Scheduled Date:</span> <?= htmlspecialchars($order['scheduled_at'] ?? 'Pending') ?></div>

        <div class="row"><span class="label">Employee:</span> <?= htmlspecialchars($order['employee_name']) ?></div>

    </div>
</div>

<?php if ($order['status'] === 'Done' || $order['status'] === 'Invoiced'): ?>
<h3>Work Report</h3>
<div class="box">
    <div><span class="label">Hours Worked:</span> <?= $order['hours_worked'] ?> hours</div>
    <div style="margin-top: 10px;">
        <strong>Notes:</strong><br>
        <?= nl2br(htmlspecialchars($order['report_text'])) ?>
    </div>
</div>
<?php endif; ?>

        <div style="margin-top: 50px; display: flex; justify-content: space-between;">
            <div style="border-top: 1px solid #000; width: 40%; text-align: center; padding-top: 5px;">
                Customer Signature
            </div>
            <div style="border-top: 1px solid #000; width: 40%; text-align: center; padding-top: 5px;">
                Employee Signature
            </div>
        </div>
        
        <div class="no-print" style="margin-top: 30px; text-align: center;">
        <a href="view.php?id=<?= $order_id ?>" style="display: inline-block; padding: 10px 20px; background-color: #ccc; color: #000; text-decoration: none; border-radius: 5px; font-weight: bold;">
            Back to Details
        </a>
    </div>

    </div>

</body>
</html>
