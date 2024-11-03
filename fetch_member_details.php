<?php
// fetch_member_details.php

include 'dbconnection.php';

// Check if member ID is provided
if (isset($_GET['id'])) {
    $member_id = intval($_GET['id']);
    
    // Prepare and execute SQL query to fetch member details
    $stmt = $conn->prepare("SELECT name, member_id, address, phone_number, email, membership_date, status, role, notes FROM member WHERE id = ?");
    $stmt->bind_param("i", $member_id);
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
            "notes" => htmlspecialchars($row['notes'])
        ];

        // Output member details as JSON
        echo json_encode($response);
    } else {
        echo json_encode(["error" => "Member not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "No member ID provided"]);
}

// Close the database connection
$conn->close();
?>
