<?php
require 'dbconnection.php'; // Include your database connection

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Prepare and bind
$query = "UPDATE members SET name = ?, address = ?, phone_number = ?, email = ?, membership_date = ?, status = ?, role = ?, notes = ? WHERE member_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param(
    "ssssssssi",
    $data['name'],
    $data['address'],
    $data['phone_number'],
    $data['email'],
    $data['membership_date'],
    $data['status'],
    $data['role'],
    $data['notes'],
    $data['member_id']
);

// Execute the statement and check for success
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
