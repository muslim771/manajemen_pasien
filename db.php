<?php
$servername = "localhost"; // Replace with your server info
$username = "your_username"; // Replace with your database username
$password = "your_password"; // Replace with your database password
$dbname = "your_database"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";  // This should show up if the connection is successful
?>

