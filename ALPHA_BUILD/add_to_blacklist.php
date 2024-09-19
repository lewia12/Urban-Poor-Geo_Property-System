<?php
include 'dbconnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $hoa_id = $_POST['hoa_id'];

    // Check if the HOA ID exists in the hoas table
    $stmt = $conn->prepare("SELECT id FROM hoas WHERE id = ?");
    $stmt->bind_param("i", $hoa_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "The selected HOA does not exist.";
        exit();
    }

    // Check if the member ID exists in the members table for the specified HOA
    $stmt = $conn->prepare("SELECT member_id FROM members WHERE member_id = ? AND hoa_id = ?");
    $stmt->bind_param("ii", $member_id, $hoa_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "The selected member does not exist in this HOA.";
        exit();
    }

    // Check if the member is already blacklisted
    $stmt = $conn->prepare("SELECT * FROM blacklist WHERE member_id = ? AND hoa_id = ?");
    $stmt->bind_param("ii", $member_id, $hoa_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "This member is already blacklisted.";
    } else {
        // Add the member to the blacklist
        $stmt = $conn->prepare("INSERT INTO blacklist (member_id, hoa_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $member_id, $hoa_id);

        if ($stmt->execute()) {
            echo "Member added to blacklist successfully.";
            header("Location: blacklist.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>
