<?php
session_start();

// Show PHP errors for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome to Hijamah Center</h1>
    <p>You are logged in as user with ID: <?php echo $_SESSION['user_id']; ?></p>

    <!-- Add navigation to other parts of the application -->
    <ul>
        <li><a href="login.php">Login</a></li>
    </ul>
</body>
</html>

