<?php
// Enable error reporting to show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session (if needed for your application)
session_start();

// Include the database connection file
include 'db.php';  // Ensure 'db.php' connects correctly to your database

// Output database connection details for debugging
echo "Servername: " . $servername . "<br>";
echo "Username: " . $username . "<br>";
echo "Password: " . $password . "<br>";
echo "DB Name: " . $dbname . "<br>";

// Check if the form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $name = $_POST['name'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $phone = $_POST['phone'];
    $sickness = $_POST['sickness'];

    // Output form data for debugging
    echo "Form data received: Name - $name, Address - $address, Age - $age, Phone - $phone, Sickness - $sickness<br>";

    // Prepare the SQL query using prepared statements to avoid SQL injection
    $query = $conn->prepare("INSERT INTO patients (name, address, age, phone, sickness) VALUES (?, ?, ?, ?, ?)");
    $query->bind_param('ssiss', $name, $address, $age, $phone, $sickness);

    // Execute the query and check if the registration was successful
    if ($query->execute()) {
        // Get the ID of the newly registered patient
        $patient_id = $conn->insert_id;

        // Temporarily disable the redirect to check output
        echo "Patient registered successfully. Patient ID: " . $patient_id;
    } else {
        // Show an error message if the query fails
        echo "Error registering patient: " . $conn->error;
    }

    // Close the query and connection
    $query->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Patient</title>
</head>
<body>
    <h1>Register a Patient</h1>

    <!-- Registration Form -->
    <form action="register.php" method="post">
        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" required><br>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" required><br>

        <label for="age">Age:</label>
        <input type="number" name="age" id="age" required><br>

        <label for="phone">Phone Number:</label>
        <input type="text" name="phone" id="phone" required><br>

        <label for="sickness">Sickness:</label>
        <textarea name="sickness" id="sickness" required></textarea><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>

