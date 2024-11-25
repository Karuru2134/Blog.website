<?php
// Database configuration
$host = "localhost";    // Hostname (usually localhost)
$username = "root";     // Your database username (default for XAMPP is "root")
$password = "";         // Your database password (leave blank for XAMPP)
$database = "media_social"; // Your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
