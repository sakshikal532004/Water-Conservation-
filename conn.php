<?php
$host = 'localhost';         // Database host (usually localhost)
$user = 'root';              // MySQL username (default is 'root' in XAMPP)
$password = 'rutu2004';              // MySQL password (empty by default in XAMPP)
$dbname = 'Water';           // Your database name

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
