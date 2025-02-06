<?php
// Database connection for admin panel
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "newgrad";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character encoding
$conn->set_charset("utf8");
?>
