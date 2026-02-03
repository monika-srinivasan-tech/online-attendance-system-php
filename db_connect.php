<?php
$servername = "localhost";
$username = "root";      // XAMPP default
$password = "";          // XAMPP default
$database = "attendance_db";  // correct database name
$port       = 3307;          // IMPORTANT: MySQL running port

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
