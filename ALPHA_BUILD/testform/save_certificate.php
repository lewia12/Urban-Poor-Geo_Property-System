<?php
// Database connection
$servername = "localhost";
$username = "root"; // Use your database username
$password = ""; // Use your database password
$dbname = "certificate_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$certificateNumber = $_POST['certificateNumber'];
$recipientName = $_POST['recipientName'];
$issueDate = $_POST['issueDate'];
$expiryDate = $_POST['expiryDate'];
$certificateType = $_POST['certificateType'];

// Sanitize and validate data
$certificateNumber = $conn->real_escape_string($certificateNumber);
$recipientName = $conn->real_escape_string($recipientName);
$issueDate = $conn->real_escape_string($issueDate);
$expiryDate = $conn->real_escape_string($expiryDate);
$certificateType = $conn->real_escape_string($certificateType);

// Insert data into the database
$sql = "INSERT INTO certificates (certificateNumber, recipientName, issueDate, expiryDate, certificateType)
        VALUES ('$certificateNumber', '$recipientName', '$issueDate', '$expiryDate', '$certificateType')";

if ($conn->query($sql) === TRUE) {
    echo "<h1>Certificate Saved Successfully</h1>";
    echo "<p><strong>Certificate Number:</strong> $certificateNumber</p>";
    echo "<p><strong>Recipient Name:</strong> $recipientName</p>";
    echo "<p><strong>Issue Date:</strong> $issueDate</p>";
    echo "<p><strong>Expiry Date:</strong> $expiryDate</p>";
    echo "<p><strong>Certificate Type:</strong> $certificateType</p>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
