<?php
include 'phpqrcode/qrlib.php'; // Path to QR code library
include 'dbconnection.php'; // Database connection file

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Specify the lot number for which to generate the QR code
$lotNumber = 'TS-18'; // Change this as needed

// Define the directory to save QR code images
$directory = 'generated_qr_code/';

// Ensure the directory exists, create it if not
if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
}

// Define the file path and name for the QR code image
$filePath = $directory . $lotNumber . '.png';

// Use the Ngrok URL instead of the local URL
$ngrokUrl = 'https://88fd-103-224-95-48.ngrok-free.app/ALPHA_BUILD/member_detail.php';
QRcode::png($ngrokUrl . '?lot_no=' . urlencode($lotNumber), $filePath);

// Check if the QR code image file is generated
if (!file_exists($filePath)) {
    die("Error generating QR Code image.");
}

// Prepare to save the file path to the database
$stmt = $conn->prepare("INSERT INTO qr_codes (lot_no, qr_image) VALUES (?, ?)");
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error); // Debugging prepare error
}
$stmt->bind_param("ss", $lotNumber, $filePath); // Bind parameters (lot_no and file path)

// Execute the statement and handle any potential error
if ($stmt->execute()) {
    $successMessage = "QR Code for Lot Number: $lotNumber saved to database successfully!";
} else {
    die("Error saving QR Code to database: " . $stmt->error); // Error handling for execute
}

// Close the statement
$stmt->close();

// Retrieve the QR code from the database for display
$stmt = $conn->prepare("SELECT qr_image FROM qr_codes WHERE id = ?");
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error); // Debugging prepare error
}
$dummyId = 18; // Change this to the ID you want to retrieve
$stmt->bind_param("i", $dummyId);
$stmt->execute();
$stmt->bind_result($qrImagePath);
$stmt->fetch();

// Check if the QR code was retrieved successfully
if ($qrImagePath && file_exists($qrImagePath)) {
    $qrImageHTML = '<img src="' . htmlspecialchars($qrImagePath) . '" alt="QR Code from Database" style="width: 100px; height: auto;" />';
} else {
    die("No QR Code found for ID: " . htmlspecialchars($dummyId)); // Debugging retrieval issue
}

// Close the statement for retrieval
$stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h3 {
            color: #333;
        }
        img {
            margin-top: 20px;
            width: 30%;
            max-width: 30%;
            height: auto;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">QR Code Generator</h2>
    
    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>

    <h3>Generated QR Code for Lot Number: <?php echo htmlspecialchars($lotNumber); ?></h3>
    <img src="<?php echo htmlspecialchars($filePath); ?>" alt="Generated QR Code" style="width: 100px; height: auto;" />

    <h3>QR Code from Database (ID: <?php echo htmlspecialchars($dummyId); ?>)</h3>
    <?php echo isset($qrImageHTML) ? $qrImageHTML : "No QR Code found."; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
