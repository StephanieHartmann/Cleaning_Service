<?php

require_once '../src/db.php';

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

try {
    if ($status_filter) {
        $sql = "SELECT * FROM ServiceOrder WHERE status = :status ORDER BY order_id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':status' => $status_filter]);
    } else {
        $stmt = $pdo->query("SELECT * FROM ServiceOrder ORDER BY order_id DESC");
    }
    $orders = $stmt->fetchAll();
} catch (Exception $e) {
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cleaning Service - Order List</title>

       <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="container">
            <h1>Cleaning Orders</h1>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">

            <a href="create.php" class="btn" style="padding: 10px 20px; background-color: #4D403A; color: white; text-decoration: none; border-radius: 5px;">Create new order</a>

            <form method="GET" style="margin: 0;">
                <label style="font-weight: bold; margin-right: 5px;">Filter:</label>

                <select name="status" onchange="this.form.submit()" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                    <option value="">All Statuses</option>
                    <option value="New" <?= $status_filter === 'New' ? 'selected' : '' ?>>New</option>
                    <option value="Scheduled" <?= $status_filter === 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
                    <option value="Cancelled" <?= $status_filter === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>

                    <option value="Completed" <?= $status_filter === 'Completed' ? 'selected' : '' ?>>Completed</option>
                    
                </select>
            </form>
        </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Cleaning Type</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Details</th>

                    </tr>
                </thead>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)):?>
                        <tr>
                            <td colspan="5" style="text-align:center">No orders found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['order_id']) ?></td>
                                <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                                <td><?= htmlspecialchars($order['cleaning_type']) ?></td>
                                
                                <td>
                                    <span class="status-<?= strtolower($order['status']) ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>

                                <td style="text-align: center;">
                                    <a href="view.php?id=<?= $order['order_id'] ?>" 
                                       style="background: #4D403A; color: white; padding: 5px 12px; text-decoration: none; border-radius: 20px; font-size: 0.8em; font-weight: bold;">
                                       View
                                    </a>
                                </td>

                                <td style="text-align: center;">
                                    <?php if ($order['status'] === 'New'): ?>
                                        <a href="schedule.php?id=<?= $order['order_id'] ?>" 
                                           style="background: #f39c12; color: white; padding: 5px 12px; text-decoration: none; border-radius: 20px; font-size: 0.8em; font-weight: bold;">
                                           Schedule
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>