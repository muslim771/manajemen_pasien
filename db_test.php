<?php
// Database connection test
$servername = "sql103.infinityfree.com";
$username = "if0_37272856";
$password = "Oraonosandi77";
$dbname = "if0_37272856_hijamah_app";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connection successful!";
}
?>

