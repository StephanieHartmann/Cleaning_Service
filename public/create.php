<?php
require_once '../scr/db.php';

if ($_SERVER['RESQUEST_METHOD'] === 'POST') {
    try {
        $sql = "INSERT INTO ServiceOrder (customer_name, phone, email, adresse, cleaning_type, size_sqm)
        VALUES (:name, :phone, :email, :address, :type, :size)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $_POST['customer_name'],
            ':phone' => $_POST['phone'],
            ':email' => $_POST['email'],
            ':address' => $_POST['address'],
            ':type' => $_POST['cleaning_type'],
            ':size' => $_POST['size_sqm'],
        ]);
        
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        $error = "Error saving order: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Order</title>
    <style>
        body{
            font-family: sans-serif;
            background-color: #FAF8F5;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #4D403A;
            color: white;
            padding: 12px 20px;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 4px;
            font-size: 16px;
        }

        button:hover {
            background-color: #333;
        }

        .cancel {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create new cleaning order</h2>

        <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>

        <form method="POST"> 
            <label>Customer Name:</label>
            <input type="text" name="customer_name" requied placeholder="e.g. Anna Zimmermann">

            <label>Phone:</label>
            <input type="text" name="phone" placeholder="+41 76 000 00 00">

            <label>Email:</label>
            <input type="text" name="address" placeholder="Main St 123">

            <label>Cleaning Type:</label>
            <select name="cleaning_type">
                <option value="Regular">Regular</option>
                <option value="Deep">Deep</option>
                <option value="Windows">Windows</option>
            </select>

            <label>Size (Square Meters):</label>
            <input type="number" name="size_sqm" placeholder="120"

            <button type="submit">Save Order</button>

            <a href="index.php" class="cancel">Cancel</a>
        </form>
    </div>
</body>
</html>