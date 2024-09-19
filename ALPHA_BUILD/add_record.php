<?php
include 'dbconnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $member_id = $_POST['member_id'];
    $hoa_id = $_POST['hoa_id'];

    // Check if member is blacklisted
    $stmt = $conn->prepare("SELECT * FROM blacklist WHERE member_id = ? AND hoa_id = ?");
    $stmt->bind_param("ii", $member_id, $hoa_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "This member is blacklisted and cannot be added.";
    } else {
        // If not blacklisted, proceed to add member
        $stmt = $conn->prepare("INSERT INTO members (name, member_id, hoa_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $member_id, $hoa_id);

        if ($stmt->execute()) {
            echo "New member added successfully";
            header("Location: hoarecords.php?id=" . $hoa_id);
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>
