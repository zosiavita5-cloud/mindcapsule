<?php

$host = 'sql106.infinityfree.com';
$dbname = 'if0_43209455_mindcapsulee';
$username = 'if0_43209455';
$password = 'akosilei1';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Database connection failed.');
}
