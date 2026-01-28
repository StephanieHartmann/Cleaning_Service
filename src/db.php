<?php

$host = 'localhost';
$db = 'cleaning_service';
$user = 'root';
$pass = '';
$port = '';

$dsn = "pgsl:host=$host;port=$port;dbname=$db";

try{
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    //
    die("Database Connection Error. (we wil fiy this later!)");
}
?>