<?php
require_once '../src/db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$order_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM ServiceOrder WHERE order_id = :id");
    $stmt->execute([':id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found!");
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= $order['order_id'] ?></title>
    <link href="css/style.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; background-color:#FAF8F5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1, h2 { color: #4D403A; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px dashed #f0f0f0; padding-bottom: 5px; }
        .label { font-weight: bold; color: #666; }
        

        .btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; color: white; display: inline-block; font-weight: bold; }
        .btn-schedule { background-color: orange; }
        .btn-print { background-color: #6c757d; }
        .btn-back { color: #666; text-decoration: none; margin-bottom: 20px; display: inline-block; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="btn-back">&larr; Back to List</a>

    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h1>Service Order #<?= $order['order_id'] ?></h1>
        
        <button onclick="window.print()" class="btn btn-print" style="border:none; cursor:pointer;">Print</button>
    </div>

    <div class="section">
        <h2>Customer Information</h2>
        <div class="info-row">
            <span class="label">Name:</span> 
            <span><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></span>
        </div>
        <div class="info-row">
            <span class="label">Phone:</span> 
            <span><?= htmlspecialchars($order['phone']) ?></span>
        </div>
        <div class="info-row">
            <span class="label">Email:</span> 
            <span><?= htmlspecialchars($order['email']) ?></span>
        </div>
        <div class="info-row">
            <span class="label">Address:</span> 
            <span><?= htmlspecialchars($order['address']) ?></span>
        </div>
    </div>

    <div class="section">
        <h2>Service Details</h2>
        <div class="info-row">
            <span class="label">Type:</span> 
            <span><?= htmlspecialchars($order['cleaning_type']) ?></span>
        </div>
        <div class="info-row">
            <span class="label">Size:</span> 
            <span><?= htmlspecialchars($order['size_sqm']) ?> m²</span>
        </div>
        <div class="info-row">
            <span class="label">Status:</span> 
            <span class="status-<?= strtolower($order['status']) ?>">
                <?= htmlspecialchars($order['status']) ?>
            </span>
        </div>
    </div>

    <?php if ($order['status'] === 'Scheduled' || $order['status'] === 'Completed' || $order['status'] === 'Invoiced'): ?>
        <div class="section">
            <h2>Schedule Information</h2>
            <div class="info-row">
                <span class="label">Assigned Employee:</span> 
                <strong><?= htmlspecialchars($order['assigned_to']) ?></strong>
            </div>
            <div class="info-row">
                <span class="label">Date:</span> 
                <span><?= date('d/m/Y', strtotime($order['service_date'])) ?></span>
            </div>
            <div class="info-row">
                <span class="label">Time:</span> 
                <span><?= date('H:i', strtotime($order['service_time'])) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="actions" style="margin-top: 40px; border-top: 2px solid #eee; padding-top: 20px;">
        
        <div style="display: flex; justify-content: space-between; align-items: center;">
            
            <form action="cancel_delete.php" method="POST" onsubmit="return confirm('Is this an error? This will PERMANENTLY delete the record from the database.');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <button type="submit" style="background: #ff4d4d; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-weight: bold;">Delete Record
                </button>
            </form>

            <div style="display:flex; gap: 10px; align-items: center;">
                
                <?php if ($order['status'] !== 'Cancelled' && $order['status'] !== 'Invoiced'): ?>
                    <form action="cancel_delete.php" method="POST" onsubmit="return confirm('Did the client cancel the job? Status will change to Cancelled.');">
                        <input type="hidden" name="action" value="cancel_order">
                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                        <button type="submit" style="background: #95a5a6; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer;"> Client Cancelled
                        </button>
                    </form>
                <?php endif; ?>

                <?php if ($order['status'] === 'New'): ?>
                    <a href="schedule.php?id=<?= $order['order_id'] ?>" 
                       style="background: orange; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                        Schedule Now
                    </a>

                <?php elseif ($order['status'] === 'Scheduled'): ?>
                    
                    <form action="cancel_delete.php" method="POST" style="display:inline;" onsubmit="return confirm('Change date? This resets to New.');">
                        <input type="hidden" name="action" value="reset_schedule">
                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                        <button type="submit" style="background: #f1c40f; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; margin-right: 10px;"> Change Date
                        </button>
                    </form>

                    <form action="cancel_delete.php" method="POST" style="display:inline;" onsubmit="return confirm('Confirm that the job is done?');">
                        <input type="hidden" name="action" value="complete_order">
                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                        <button type="submit" style="background: #27ae60; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-weight: bold;">Mark as Done
                        </button>
                    </form>

                <?php endif; ?>

    </div>
</div>

</body>
</html>