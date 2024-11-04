<?php
// fetch_member_details.php

include 'dbconnection.php';

// Check if lot_no is provided (assuming QR code is tied to lot_no)
if (isset($_GET['lot_no'])) {
    $lot_no = $_GET['lot_no'];
    
    // Prepare and execute SQL query to fetch member details, including lot_no and QR code
    $stmt = $conn->prepare("SELECT name, member_id, address, phone_number, email, membership_date, status, role, notes, lot_no, qr_code FROM member WHERE lot_no = ?");
    
    if ($stmt === false) {
        // Handle prepare statement error
        echo json_encode(["error" => "Database prepare error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("s", $lot_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch member details
        $row = $result->fetch_assoc();
        
        // Ensure all data is properly encoded for JSON output
        $response = [
            "name" => htmlspecialchars($row['name']),
            "member_id" => htmlspecialchars($row['member_id']),
            "address" => htmlspecialchars($row['address']),
            "phone_number" => htmlspecialchars($row['phone_number']),
            "email" => htmlspecialchars($row['email']),
            "membership_date" => htmlspecialchars($row['membership_date']),
            "status" => htmlspecialchars($row['status']),
            "role" => htmlspecialchars($row['role']),
            "notes" => htmlspecialchars($row['notes']),
            "lot_no" => htmlspecialchars($row['lot_no']),
            "qr_code" => base64_encode($row['qr_code']) // Encode QR code for JSON output
        ];

        // Output member details as JSON
        echo json_encode($response);
    } else {
        echo json_encode(["error" => "Member not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "No lot number provided"]);
}

// Close the database connection
$conn->close();
?>
