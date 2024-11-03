<?php
include 'phpqrcode/qrlib.php'; // Ensure the path to the library is correct
include 'dbconnection.php'; // Your database connection file

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Specify the lot number for which to generate the QR code
$lotNumber = 'TS-16'; // Change this as needed

// Start output buffering
ob_start();
QRcode::png('http://yourwebsite.com/details.php?lot_no=' . urlencode($lotNumber)); // Generate QR code
$imageData = ob_get_contents(); // Get the binary image data
ob_end_clean(); // Clean output buffer

// Check if the QR code image data is generated
if ($imageData === false) {
    die("Error generating QR Code image.");
}

// Encode the image data to Base64
$base64Image = base64_encode($imageData);

// Prepare to save the Base64 string to the database
$stmt = $conn->prepare("INSERT INTO qr_codes (lot_no, qr_image) VALUES (?, ?)");
$stmt->bind_param("ss", $lotNumber, $base64Image); // Bind parameters (string for lot_no, string for qr_image)

// Execute the statement
if ($stmt->execute()) {
    $successMessage = "QR Code for Lot Number: $lotNumber saved to database successfully!";
} else {
    die("Error saving QR Code to database: " . $stmt->error); // Change to die() for debugging
}

// Close the statement
$stmt->close();

// Now retrieve the QR code from the database for display
$stmt = $conn->prepare("SELECT qr_image FROM qr_codes WHERE id = ?");
$dummyId = 7; // Change this to the ID you want to retrieve
$stmt->bind_param("i", $dummyId);
$stmt->execute();
$stmt->bind_result($qrImage);
$stmt->fetch();

// Check if the QR code was retrieved successfully
if ($qrImage) {
    $qrImageHTML = '<img src="data:image/png;base64,' . $qrImage . '" alt="QR Code" style="width: 100px; height: auto;" />';
} else {
    die("No QR Code found for ID: " . htmlspecialchars($dummyId)); // Change to die() for debugging
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
            background-color: #f8f9fa;
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
            width: 100%;
            max-width: 300px; /* Limit the size of the QR code image */
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

    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>

    <h3>QR Code for Lot Number: <?php echo htmlspecialchars($lotNumber); ?></h3>
    <img src="data:image/png;base64,<?php echo $base64Image; ?>" alt="Generated QR Code" />
    
    <h3>QR Code from Database (ID: <?php echo htmlspecialchars($dummyId); ?>)</h3>
    <?php echo isset($qrImageHTML) ? $qrImageHTML : "No QR Code found."; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
