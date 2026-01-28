<?php

require_once '../src/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM ServiceOrder ORDER BY order_id DESC");
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
            <a href="create.php" class="btn">Create new order</a>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Cleaning Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)):?>
                        <tr>
                            <td colspan="5"
                            style="text-align:center">No orders found (or database not connected). </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['order_id']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($order['cleaning_type']) ?></td>
                                    <td>
                                        <span class="status-<?= strtolower($order['status']) ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                    </td>
                                    <td>
                                        <a href="view.php?id=<?=  $order['order_id'] ?>">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>