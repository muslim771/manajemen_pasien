<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db.php';  // Ensure the database connection is correct

// Your existing code here...
?>


// Initialize search term
$search_term = trim($_POST['search_term'] ?? '');

// Fetch patients from the database with optional search term
$query = "SELECT * FROM patients";
if ($search_term) {
    $search_term = '%' . $search_term . '%';
    $query .= " WHERE name LIKE ?";
}
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Database query failed: " . $conn->error);
}
if ($search_term) {
    $stmt->bind_param('s', $search_term);
}
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any records
if ($result->num_rows > 0) {
    $patients = $result->fetch_all(MYSQLI_ASSOC);  // Fetch all records as an associative array
} else {
    $patients = [];
}

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patients</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .patient-link {
            color: blue;
            text-decoration: underline;
        }
        .back-button, .search-form {
            margin-bottom: 20px;
        }
        .back-button a {
            text-decoration: none;
        }
        .back-button button, .search-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-button button:hover, .search-form button:hover {
            background-color: #0056b3;
        }
        .search-form input[type="text"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

    <h2>Patients List</h2>

    <!-- Search Form -->
    <div class="search-form">
        <form action="view_patients.php" method="post">
            <label for="search_term">Search by Name:</label>
            <input type="text" name="search_term" id="search_term" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Enter name">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Back to Index Button -->
    <div class="back-button">
        <a href="index.php"><button type="button">Back to Index</button></a>
    </div>

    <?php if (count($patients) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Address</th>
                    <th>Age</th>
                    <th>Phone</th>
                    <th>Sickness</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($patient['id']); ?></td>
                        <!-- Make the patient's name clickable -->
                        <td>
                            <a class="patient-link" href="hijamah_point_recommender.php?patient_id=<?php echo urlencode($patient['id']); ?>&patient_name=<?php echo urlencode($patient['name']); ?>&patient_complaint=<?php echo urlencode($patient['sickness']); ?>">
                                <?php echo htmlspecialchars($patient['name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($patient['address']); ?></td>
                        <td><?php echo htmlspecialchars($patient['age']); ?></td>
                        <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                        <td><?php echo htmlspecialchars($patient['sickness']); ?></td>
                        <td><?php echo date('d M Y H:i:s', strtotime($patient['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No patients found.</p>
    <?php endif; ?>

</body>
</html>

