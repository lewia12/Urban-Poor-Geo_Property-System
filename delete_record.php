<?php
session_start(); // Start the session

include 'dbconnection.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get member ID and HOA from the POST request
    $member_id = $_POST['member_id'];
    $hoa = $_POST['hoa']; // Retrieve HOA for redirection after deletion

    // Prepare and bind the DELETE statement
    $stmt = $conn->prepare("DELETE FROM member WHERE id = ?"); // Prepare SQL statement
    $stmt->bind_param("i", $member_id); // Bind the member ID as an integer

    // Attempt to execute the statement
    if ($stmt->execute()) {
        // Record deleted successfully
        $_SESSION['success_message'] = "Member deleted successfully."; // Set a success message in session
        header("Location: hoarecords.php?hoa=" . urlencode($hoa)); // Redirect back to the HOA records page
        exit(); // Exit to prevent further script execution
    } else {
        // Error during deletion
        $_SESSION['error_message'] = "Error deleting member: " . $stmt->error; // Set error message in session
        header("Location: hoarecords.php?hoa=" . urlencode($hoa)); // Redirect back to HOA records page
        exit();
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
