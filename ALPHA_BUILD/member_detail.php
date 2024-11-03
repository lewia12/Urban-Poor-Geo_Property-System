<?php
include 'dbconnection.php';

// Initialize member data
$member = null;

// Check if lot_no is provided
if (isset($_GET['lot_no'])) {
    $lot_no = $_GET['lot_no'];
    
    // Fetch member details using the lot_no
    $stmt = $conn->prepare("SELECT * FROM member WHERE lot_no = ?");
    $stmt->bind_param("s", $lot_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $member = $result->fetch_assoc();
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Details</title>
    <link rel="stylesheet" href="recordstyle.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom styles for the QR code scanner */
        #reader {
            width: 100%; /* Full width of the container */
            height: 300px; /* Set height to 300px for a smaller display */
            margin-bottom: 20px;
            border: 1px solid #ccc; /* Optional: Add a border for better visibility */
            background-color: rgba(0, 0, 0, 0.5); /* Optional: Add a shaded background */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">GIS System</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="records.php">Record</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <h1>Member Details</h1>

                <!-- Member Details Section -->
                <div id="memberDetails">
                    <?php if ($member): ?>
                        <h2>Details for Lot No: <?= htmlspecialchars($member['lot_no']) ?></h2>
                        <p><strong>Name:</strong> <?= htmlspecialchars($member['name']) ?></p>
                        <p><strong>Member ID:</strong> <?= htmlspecialchars($member['member_id']) ?></p>
                        <p><strong>Address:</strong> <?= htmlspecialchars($member['address']) ?></p>
                        <p><strong>Phone Number:</strong> <?= htmlspecialchars($member['phone_number']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($member['email']) ?></p>
                        <p><strong>Membership Date:</strong> <?= htmlspecialchars($member['membership_date']) ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($member['status']) ?></p>
                        <p><strong>Role:</strong> <?= htmlspecialchars($member['role']) ?></p>
                        <p><strong>Notes:</strong> <?= htmlspecialchars($member['notes']) ?></p>
                    <?php else: ?>
                        <p>No member details available. Please scan a QR code.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-6">
                <!-- QR Code Scanner Section -->
                <h1>QR Code Scanner</h1>
                <div id="reader"></div>
                <p id="qrResult" style="font-weight: bold;"></p>
            </div>
        </div>
    </div>

    <!-- Load JS libraries -->
    <script src="./html5-qrcode.min.js"></script> <!-- Ensure correct path -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    // Function to check camera permissions
    function checkCameraPermissions() {
        return navigator.mediaDevices.getUserMedia({ video: true })
            .then(() => true)
            .catch(() => false);
    }

    // Initialize the QR code scanner
    const html5QrCode = new Html5Qrcode("reader");

    // Check for camera permissions and start the scanner
    checkCameraPermissions().then(permissionGranted => {
        if (permissionGranted) {
            // Start the QR code scanner with the front camera
            html5QrCode.start(
                { facingMode: "user" }, // Use the front camera
                {
                    fps: 10,
                    qrbox: { width: 100, height: 100 } // Adjust QR box size to 100px x 100px
                },
                (decodedText, decodedResult) => {
                    // When a QR code is scanned, extract lot_no and redirect
                    const params = new URLSearchParams(decodedText.split('?')[1]);
                    const lot_no = params.get('lot_no'); // Assuming QR code contains lot_no
                    if (lot_no) {
                        window.location.href = "member_details.php?lot_no=" + lot_no;
                    }
                },
                (errorMessage) => {
                    // Handle scan errors if necessary
                }
            ).catch(err => {
                console.error('Error starting QR code scanner:', err);
            });
        } else {
            alert("Camera access is required to scan QR codes. Please allow camera access in your browser settings.");
        }
    }).catch(err => {
        console.error('Error checking camera permissions:', err);
    });

    // Stop scanning when leaving the page
    window.addEventListener('beforeunload', () => {
        html5QrCode.stop().catch(err => {
            console.error('Error stopping scanner:', err);
        });
    });
    </script>
</body>
</html>
