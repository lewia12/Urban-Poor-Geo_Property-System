<?php
session_start(); // Start the session
include 'dbconnection.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// SQL query to fetch unique HOA names from the polygons table
$sql = "SELECT DISTINCT hoa FROM polygons";  // Fetch unique HOA values
$result = $conn->query($sql);

$hoas = [];

// Check if there are results and output each HOA name
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hoas[] = $row["hoa"]; // Store the HOA names in an array
    }
}

// Return the HOA names as a JSON response
header('Content-Type: application/json');
echo json_encode($hoas);

// Close the database connection
$conn->close();
?>
