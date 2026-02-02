<?php

$host = 'aws-1-eu-west-1.pooler.supabase.com';
$db = 'postgres';
$user = 'postgres.drlovtrxaxhnzdndbyxw';
$pass = 'LFSRubemExhcf3YA';
$port = '6543';

// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

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