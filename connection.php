<?php
// Database configuration settings
$host = "localhost";       // Your database host, usually localhost
$username = "root";        // Your database username
$password = "";            // Your database password
$database = "nsu_event_management"; // Your database name

// Create connection using MySQLi
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Close the connection in other scripts after using it
// $conn->close();
?>
