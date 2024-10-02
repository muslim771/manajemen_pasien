<?php
session_start();
include 'db.php';  // Make sure this file has your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form inputs
    $name = htmlspecialchars($_POST['name']);
    $address = htmlspecialchars($_POST['address']);
    $age = intval($_POST['age']);
    $phone = htmlspecialchars($_POST['phone']);
    $sickness = htmlspecialchars($_POST['sickness']);

    // Check if all the required fields are provided
    if (!empty($name) && !empty($address) && $age > 0 && !empty($phone) && !empty($sickness)) {
        // Prepare the SQL query
        $query = $conn->prepare("INSERT INTO patients (name, address, age, phone, sickness) VALUES (?, ?, ?, ?, ?)");
        $query->bind_param('ssiss', $name, $address, $age, $phone, $sickness);

        // Execute the query and check if it is successful
        if ($query->execute()) {
            // If patient data is saved successfully, redirect to the login page
            header("Location: login.php");
            exit();  // Ensure script stops executing after redirect
        } else {
            echo "Error saving patient data: " . $conn->error;  // Display detailed error message for debugging
        }

        // Close the statement
        $query->close();
    } else {
        echo "All fields are required.";
    }

    // Close the database connection
    $conn->close();
}

