<?php
$pdo = new PDO("mysql:host=localhost;dbname=healthsphere", "root", "");

$username = "hsphere15@gmail.com";
$password = password_hash("HSphere331412", PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
$stmt->execute([$username, $password]);

echo "Admin created!";
?>