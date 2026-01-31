<?php
require_once '../src/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try { 
        if(empty($_POST['first_name']) || empty($_POST['last_name']) || 
           empty($_POST['phone']) || empty($_POST['email']) || 
           empty($_POST['street']) || empty($_POST['number']) || 
           empty($_POST['zip']) || empty($_POST['city']) || 
           empty($_POST['size_sqm'])) {
            
            throw new Exception("Please fill in all required fields.");
        }

        $size = !empty($_POST['size_sqm']) ? $_POST['size_sqm'] : null;

        $size = $_POST['size_sqm'];

        $full_address = $_POST['street'] . ' ' . $_POST['number'] . ', ' . $_POST['zip'] . ' ' . $_POST['city'];

        $sql = "INSERT INTO ServiceOrder (first_name, last_name, phone, email, address, cleaning_type, size_sqm)
                VALUES (:fname, :lname, :phone, :email, :address, :type, :size)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':fname' => $_POST['first_name'],
            ':lname' => $_POST['last_name'],
            ':phone' => $_POST['phone'],
            ':email' => $_POST['email'],
            ':address' => $full_address,
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

        h1 { color: #4D403A; 
        text-align: center; 
        margin-bottom: 20px;
        }

        /* Required field*/
        label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold;
            color: #4D403A; 
        }

        /* Red * */
        .req-star { 
            color: red; 
            margin-left: 3px; 
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .row-names { 
            display: flex; 
            gap: 15px;
        }

        .col-half { 
            flex: 1; 
        }


        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        button, .btn-cancel {
            padding: 10px 20px;
            border-radius: 6px; 
            font-size: 14px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            width: 100%;
            flex: 1;
            box-sizing: border-box;
            text-align: center;
            text-decoration: none;
            display: flex;
            transition: background 0.3s;
            justify-content: center;
            white-space: nowrap;
        }

        button {
            background-color: #4D403A;
            color: white;
        }

        button:hover {
            background-color: #333;
        }

        .btn-cancel {
            background-color: #e0e0e0;
            color: #4D403A;
        }

        .btn-cancel:hover { 
            background-color: #bbb; 
        }

        .error-msg { 
            color: red; 
            background: #ffe6e6; 
            padding: 10px; 
            border-radius: 4px; 
            margin-bottom: 20px; 
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>Create new cleaning order</h2>

        <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>

        <form method="POST"> 

            <div class="row-names">
                <div class="col-half">
                    <label>First Name: <span class="req-star">*</span></label>
                    <input type="text" name="first_name" required placeholder="e.g. Anna">
                </div>

                <div class="col-half">
                    <label>Last Name: <span class="req-star">*</span></label>
                    <input type="text" name="last_name" required placeholder="e.g. Zimmermann">
                </div>
            </div>

            <label>Phone: <span style="color:red">*</span></label>
            <input type="tel" name="phone" required 
               placeholder="+41 76 123 45 67" 
               pattern="[\+0-9\s]+" 
               title="Allow numbers, spaces and + (e.g. +41 76 000 00 00)">

            <label>Email: <span style="color:red">*</span></label>
            <input type="email" name="email" required 
               placeholder="name@example.com" 
               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
               title="Must contain an @ and a domain (like .com)">

            <label>Address: <span class="req-star">*</span></label>
            <div style="display: flex; gap: 15px;">
                <div style="flex: 3;">
                    <input type="text" name="street" required placeholder="Street Name" style="margin-top: 0;">
                </div>
                <div style="flex: 1;">
                    <input type="text" name="number" required
                    placeholder="Nr." 
                    style="margin-top: 0;"
                    inputmode="numeric"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 10px;">

                <div style="flex: 1;">
                    <input type="text" name="zip" required
                    placeholder="ZIP" 
                    style="margin-top: 0;"
                    inputmode="numeric"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                </div>

                <div style="flex: 3;">
                    <input type="text" name="city" required placeholder="City" style="margin-top: 0;">
                </div>
            </div>
                

            <label>Cleaning Type: <span class="req-star">*</span></label>
            <select name="cleaning_type" required>
                <option value="Regular">Regular</option>
                <option value="Deep">Deep</option>
                <option value="Windows">Windows</option>
                <option value="Laundry">Laundry (Washing)</option>
                <option value="Ironing">Ironing</option>
            </select>

            <label>Size (Square Meters): <span class="req-star">*</span></label>
            <input type="number" name="size_sqm" required placeholder="e.g. 120">

            <div style="margin-top: 20px; display:flex; gap: 10px;">

            <div class="btn-group">
                <button type="submit" class="btn btn-save">Save Order</button>
                <a href="index.php" class="btn btn-cancel">Cancel</a>

            </div>
        </form>
    </div>
</body>
</html>