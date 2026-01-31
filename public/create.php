<?php
require_once '../src/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $size = !empty($_POST['size_sqm']) ? $_POST['size_sqm'] : null;

        $sql = "INSERT INTO ServiceOrder (customer_name, phone, email, address, cleaning_type, size_sqm)
                VALUES (:name, :phone, :email, :address, :type, :size)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $_POST['customer_name'],
            ':phone' => $_POST['phone'],
            ':email' => $_POST['email'],
            ':address' => $_POST['address'],
            ':type' => $_POST['cleaning_type'],
            ':size' => $size,

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
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #4D403A; 
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
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
            flex: 1;
        }

        button:hover {
            background-color: #333;
        }

        .btn-cancel {
            background-color: #ccc;
            color: #666;
            text-decoration: none;
            padding:  12px 20px;
            border-radius: 4px;
            text-align: center;
            flex: 1;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create new cleaning order</h2>

        <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>

        <form method="POST"> 
            <label>Customer Name:</label>
            <input type="text" name="customer_name" required placeholder="e.g. Anna Zimmermann">

            <label>Phone:</label>
            <input type="text" name="phone" placeholder="+41 76 000 00 00">

            <label>Email:</label>
            <input type="text" name="email" placeholder="anna@example.com">

            <label>Address:</label>
            <input type="text" name="address" placeholder="Main St 123">

            <label>Cleaning Type:</label>
            <select name="cleaning_type">
                <option value="Regular">Regular</option>
                <option value="Deep">Deep</option>
                <option value="Windows">Windows</option>
            </select>

            <label>Size (Square Meters):</label>
            <input type="number" name="size_sqm" placeholder="120">

            <div style="margin-top: 20px; display:flex; gap: 10px;">

            <button type="submit" class="btn" style="flex: 1;">Save Order</button>

            </div>

            <div style="margin-top: 20px; display: flex; gap: 10px;">

            <a href="index.php" class="btn" style="background-color: #ccc; color: #333; text-align: center; width: 100px;">Cancel</a>

            </div>
        </form>
    </div>
</body>
</html>