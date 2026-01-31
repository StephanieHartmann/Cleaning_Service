<?php

$host = 'aws-1-eu-west-1.pooler.supabase.com';
$db = 'postgres';
$user = 'postgres.drlovtrxaxhnzdndbyxw';
$pass = 'LFSRubemExhcf3YA';
$port = '6543';

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

try{
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    //
    throw new Exception("Database Connection Error: " . $e->getMessage());
}
?>