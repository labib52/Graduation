<?php
// Database credentials
$servername = "localhost"; // Replace with your server name (e.g., 127.0.0.1)
$username = "root";        // Replace with your database username
$password = "";            // Replace with your database password
$dbname = "newgrad"; // Replace with your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
