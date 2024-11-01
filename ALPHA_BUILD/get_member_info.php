<?php
include 'dbconnection.php'; // Include your existing DB connection file

$hoa_id = isset($_GET['hoa_id']) ? intval($_GET['hoa_id']) : 0; // Get HOA ID from the request
$sql = "SELECT * FROM members WHERE hoa_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hoa_id);
$stmt->execute();
$result = $stmt->get_result();

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($members);
?>
