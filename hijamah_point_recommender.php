<?php
session_start();
include 'db.php';  // Include the database connection

// Initialize variables to hold patient data
$patient_name = '';
$patient_sickness = '';
$recommendation_error = '';
$patient_id = $_GET['patient_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch the patient's name from the form input
    $patient_name = $_POST['patientName'];

    // Query to find the patient by name
    $stmt = $conn->prepare("SELECT id, name, sickness FROM patients WHERE name = ?");
    $stmt->bind_param("s", $patient_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the patient's data
        $patient_data = $result->fetch_assoc();
        $patient_id = $patient_data['id'];
        $patient_name = $patient_data['name'];
        $patient_sickness = $patient_data['sickness'];
    } else {
        $recommendation_error = "Patient not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hijamah Point Recommender</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
        }

        .result-section {
            margin-top: 20px;
        }

        .result {
            white-space: pre-wrap;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }

        #bodyDiagram {
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Hijamah Point Recommender</h1>
        <p class="text-center">Masukkan nama pasien untuk mendapatkan rekomendasi titik hijamah berdasarkan keluhan.</p>

        <!-- Form for searching a patient by name -->
        <form method="POST">
            <div class="form-group">
                <label for="patientName">Nama Pasien:</label>
                <input type="text" class="form-control" id="patientName" name="patientName" placeholder="Nama pasien" value="<?php echo htmlspecialchars($patient_name); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Cari Pasien</button>
        </form>

        <?php if ($recommendation_error): ?>
            <div class="alert alert-danger"><?php echo $recommendation_error; ?></div>
        <?php elseif ($patient_sickness): ?>
            <div class="result-section">
                <h3>Keluhan Pasien: <?php echo htmlspecialchars($patient_sickness); ?></h3>
                <h3>Rekomendasi Titik Hijamah:</h3>
                <div class="result" id="recommendedPoints"></div>

                <h3>Visualisasi Titik Hijamah:</h3>
                <img src="" id="bodyDiagram" alt="Body Diagram">
                <div id="variationButtons"></div>

                <!-- Done Button -->
                <form action="laporan_hasil.php" method="post">
                    <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patient_id); ?>">
                    <input type="hidden" name="action_taken" value="Rekomendasi Titik Hijamah: <?php echo htmlspecialchars($patient_sickness); ?>">
                    <button type="submit" class="btn btn-success">Tindakan Sudah Dilakukan</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Data titik hijamah dengan variasi
        var hijamahPoints = {
            "sakit kepala": [
                {
                    "description": "Titik Al-Kahil dan Al-Akhda'ain",
                    "points": ["Al-Kahil", "Al-Akhda'ain"],
                    "image": "https://safira24.biz.id/gambarbebas/20240908-070535_kahil.jpg"
                },
                {
                    "description": "Titik Yafukh dan Dahi",
                    "points": ["Yafukh", "Dahi"],
                    "image": "https://safira24.biz.id/gambarbebas/20240908-065105_yafukh1.jpg"
                }
            ],
            "nyeri punggung": [
                {
                    "description": "Titik Ummu Mughits dan Punggung Tengah",
                    "points": ["Ummu Mughits", "Punggung Tengah"],
                    "image": "https://www.example.com/back-pain-diagram.jpg"
                }
            ],
            "masalah pencernaan": [
                {
                    "description": "Titik Perut Bawah dan Titik Pinggang",
                    "points": ["Perut Bawah", "Pinggang"],
                    "image": "https://www.example.com/digestion-diagram.jpg"
                }
            ],
            "kelelahan": [
                {
                    "description": "Titik Punggung Bawah dan Betis",
                    "points": ["Punggung Bawah", "Betis"],
                    "image": "https://www.example.com/fatigue-diagram.jpg"
                }
            ]
        };

        // Automatically recommend points if sickness is found
        var patientSickness = "<?php echo $patient_sickness; ?>";

        if (patientSickness) {
            recommendForPatient(patientSickness);
        }

        // Function to recommend hijamah points for a specific patient complaint
        function recommendForPatient(complaint) {
            var recommendations = hijamahPoints[complaint];

            if (!recommendations) {
                document.getElementById('recommendedPoints').innerText = "Tidak ada rekomendasi yang tersedia untuk keluhan ini.";
                return;
            }

            var output = "";
            var variationButtons = "";

            recommendations.forEach(function(rec, index) {
                output += "Pilihan " + (index + 1) + ":\n";
                output += rec.description + "\n\n";
                variationButtons += "<button class='btn btn-info' onclick='showVariation(" + index + ")'>Lihat Pilihan " + (index + 1) + "</button> ";
            });

            document.getElementById('recommendedPoints').innerText = output;
            document.getElementById('variationButtons').innerHTML = variationButtons;

            // Show the first image as default
            showVariation(0);
        }

        // Function to display the chosen variation
        function showVariation(index) {
            var recommendations = hijamahPoints[patientSickness];

            if (recommendations && recommendations[index] && recommendations[index].image) {
                document.getElementById('bodyDiagram').src = recommendations[index].image;
            } else {
                document.getElementById('bodyDiagram').src = "";
            }
        }
    </script>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

