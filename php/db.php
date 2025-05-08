<?php
$host = "localhost"; // Change to your database host if needed
$dbname = "user_system"; // Your database name
$username = "root"; // Your database username
$password = ""; // Your database password (leave empty if using XAMPP)

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
