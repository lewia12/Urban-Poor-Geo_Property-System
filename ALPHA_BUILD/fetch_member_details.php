<?php
// fetch_member_details.php

include 'dbconnection.php';

// Check if member ID is provided
if (isset($_GET['id'])) {
    $member_id = intval($_GET['id']);
    
    // Prepare and execute SQL query to fetch member details
    $stmt = $conn->prepare("SELECT name, member_id, address, phone_number, email, membership_date, status, role, notes FROM members WHERE id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch member details
        $row = $result->fetch_assoc();
        // Output member details as JSON
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Member not found"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "No member ID provided"]);
}
?>
