<?php
include 'dbconnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $hoa_id = $_POST['hoa_id'];

    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
    $stmt->bind_param("i", $member_id);

    // Execute the query
    if ($stmt->execute()) {
        echo "Record deleted successfully";
        header("Location: hoarecords.php?id=" . $hoa_id); // Redirect back to the HOA records page
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
