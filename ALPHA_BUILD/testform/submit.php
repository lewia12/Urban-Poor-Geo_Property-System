<?php
// Retrieve form data
$certificateNumber = htmlspecialchars($_POST['certificateNumber']);
$recipientName = htmlspecialchars($_POST['recipientName']);
$issueDate = htmlspecialchars($_POST['issueDate']);
$expiryDate = htmlspecialchars($_POST['expiryDate']);
$certificateType = htmlspecialchars($_POST['certificateType']);

// Display the submitted data
echo "<h1>Certificate Submitted Successfully</h1>";
echo "<p><strong>Certificate Number:</strong> $certificateNumber</p>";
echo "<p><strong>Recipient Name:</strong> $recipientName</p>";
echo "<p><strong>Issue Date:</strong> $issueDate</p>";
echo "<p><strong>Expiry Date:</strong> $expiryDate</p>";
echo "<p><strong>Certificate Type:</strong> $certificateType</p>";

// Here you could add code to save the data to a database if desired.
?>
