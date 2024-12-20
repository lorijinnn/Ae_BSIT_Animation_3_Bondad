<?php
$host = 'localhost'; // or '127.0.0.1'
$username = 'root'; // default XAMPP MySQL username
$password = ''; // default XAMPP MySQL password is empty
$dbname = 'aezyfloral'; // your database name

// Create a connection to the MySQL database
$conn = new mysqli($host, $username, $password, $dbname);

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
