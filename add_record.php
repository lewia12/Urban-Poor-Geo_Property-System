<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbconnection.php';

// Check if the form was submitted and the required data is present
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data, ensuring HOA value is retrieved from the form
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
        // Respond with an error message if HOA is not set
        echo json_encode(['error' => 'Error: HOA cannot be null or empty']);
        exit;
    }

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO member (name, member_id, address, phone_number, email, membership_date, status, role, notes, hoa, lot_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $name, $member_id, $address, $phone_number, $email, $membership_date, $status, $role, $notes, $hoa, $lot_no);

    if ($stmt->execute()) {
        // Respond with a success message
        echo json_encode(['success' => true, 'hoa' => $hoa]);
    } else {
        // Respond with an error message
        echo json_encode(['error' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    // Handle the case where the request is not a POST
    echo json_encode(['error' => 'Invalid request']);
}

$conn->close();
?>
