<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';  // Database connection

// Initialize patient variable
$patient = [];

// Get the patient ID from the query string
$patient_id = $_GET['patient_id'] ?? null;

if ($patient_id) {
    // Fetch patient details from the database
    $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the patient's data
        $patient = $result->fetch_assoc();
    } else {
        echo "Patient not found!";
        exit();
    }

    $stmt->close();
}

// Handle form submission for suggesting the next appointment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $next_appointment = $_POST['next_appointment'] ?? '';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $action_taken = $_POST['action_taken'] ?? '';

    if ($patient_id) {
        // Update the patient with diagnosis, next appointment suggestion, and actions taken
        $stmt = $conn->prepare("UPDATE patients SET next_appointment = ?, diagnosis = ?, action_taken = CONCAT(IFNULL(action_taken, ''), ?) WHERE id = ?");
        $stmt->bind_param("sssi", $next_appointment, $diagnosis, $action_taken, $patient_id);
        $stmt->execute();
        $stmt->close();
        echo "Saran jadwal bekam selanjutnya, diagnosa, dan tindakan telah disimpan.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Tindakan</title>
</head>
<body>
    <h1>Laporan Hasil Tindakan</h1>

    <!-- Display patient details -->
    <p><strong>Nama:</strong> <?php echo isset($patient['name']) ? htmlspecialchars($patient['name']) : 'Data tidak tersedia'; ?></p>
    <p><strong>Alamat:</strong> <?php echo isset($patient['address']) ? htmlspecialchars($patient['address']) : 'Data tidak tersedia'; ?></p>
    <p><strong>Umur:</strong> <?php echo isset($patient['age']) ? htmlspecialchars($patient['age']) : 'Data tidak tersedia'; ?></p>
    <p><strong>No. Telepon:</strong> <?php echo isset($patient['phone']) ? htmlspecialchars($patient['phone']) : 'Data tidak tersedia'; ?></p>
    <p><strong>Keluhan:</strong> <?php echo isset($patient['sickness']) ? htmlspecialchars($patient['sickness']) : 'Data tidak tersedia'; ?></p>

    <h2>Diagnosa</h2>
    <p><?php echo isset($patient['diagnosis']) && $patient['diagnosis'] ? htmlspecialchars($patient['diagnosis']) : 'Diagnosa belum tersedia'; ?></p>

    <h2>Tindakan Sebelumnya</h2>
    <p><?php echo isset($patient['action_taken']) && $patient['action_taken'] ? htmlspecialchars($patient['action_taken']) : 'Belum ada tindakan sebelumnya'; ?></p>

    <h2>Kolom Saran untuk Jadwal Bekam Selanjutnya</h2>
    <form action="laporan_hasil.php?patient_id=<?php echo urlencode($patient_id); ?>" method="post">
        <label for="diagnosis">Diagnosa:</label>
        <textarea name="diagnosis" id="diagnosis" rows="4" cols="50"><?php echo isset($patient['diagnosis']) ? htmlspecialchars($patient['diagnosis']) : ''; ?></textarea><br>
        
        <label for="action_taken">Tindakan yang Sudah Dilakukan:</label>
        <textarea name="action_taken" id="action_taken" rows="4" cols="50"><?php echo isset($patient['action_taken']) ? htmlspecialchars($patient['action_taken']) : ''; ?></textarea><br>
        
        <label for="next_appointment">Saran Jadwal Bekam Selanjutnya:</label>
        <input type="date" name="next_appointment" id="next_appointment" value="<?php echo isset($patient['next_appointment']) ? htmlspecialchars($patient['next_appointment']) : ''; ?>" required><br>
        
        <button type="submit">Simpan Saran, Diagnosa, dan Tindakan</button>
    </form>

    <!-- Logout button -->
    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>

</body>
</html>

