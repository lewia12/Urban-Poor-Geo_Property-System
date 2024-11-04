<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbconnection.php';
require_once 'phpqrcode/qrlib.php'; // Ensure you include the QR code library

// Function to generate QR code as binary data
function generateQRCodeAsBinary($lot_no) {
    ob_start(); // Start output buffering
    QRcode::png("http://yourwebsite.com/member_details.php?lot_no=" . urlencode($lot_no), null, QR_ECLEVEL_L, 10);
    $imageData = ob_get_contents(); // Get the contents of the buffer
    ob_end_clean(); // Clean the buffer and end it
    return $imageData; // Return binary image data
}

// Check if the form was submitted and the required data is present
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $hoa = isset($_POST['hoa']) ? $_POST['hoa'] : null; // HOA value from the form
    $name = $_POST['name'];
    $member_id = $_POST['member_id'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $membership_date = $_POST['membership_date'];
    $status = $_POST['status'];
    $role = $_POST['role'];
    $notes = $_POST['notes'];
    $lot_no = $_POST['lot_no'];

    // Validate HOA
    if ($hoa === null || empty($hoa)) {
        echo json_encode(['error' => 'Error: HOA cannot be null or empty']);
        exit;
    }

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO member (name, member_id, address, phone_number, email, membership_date, status, role, notes, hoa, lot_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param("sssssssssss", $name, $member_id, $address, $phone_number, $email, $membership_date, $status, $role, $notes, $hoa, $lot_no);

    // Execute and check for success
    if ($stmt->execute()) {
        // Generate the QR code as binary data bound to lot_no
        $qrCodeData = generateQRCodeAsBinary($lot_no);

        // Update the member record with the QR code binary data
        $stmt = $conn->prepare("UPDATE member SET qr_code = ? WHERE lot_no = ?");
        $stmt->bind_param("bs", $qrCodeData, $lot_no); // Use 'b' for BLOB
        $stmt->execute();

        // Respond with a success message
        echo json_encode(['success' => true, 'hoa' => $hoa]);
    } else {
        // Respond with an error message
        echo json_encode(['error' => 'Error: ' . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}

// Close the connection
$conn->close();
?>
