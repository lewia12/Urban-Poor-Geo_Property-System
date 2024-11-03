<?php
include 'dbconnection.php';

header('Content-Type: application/json');

if (isset($_GET['hoa'])) {
    $hoa = $_GET['hoa'];

    // Prepare the statement to fetch lot numbers for the selected HOA
    $stmt = $conn->prepare("
        SELECT lot_no 
        FROM polygons 
        WHERE hoa = ? AND lot_no NOT IN (SELECT lot_no FROM member WHERE hoa = ?)
    ");
    $stmt->bind_param("ss", $hoa, $hoa);
    $stmt->execute();
    $result = $stmt->get_result();

    $lots = [];
    while ($row = $result->fetch_assoc()) {
        $lots[] = $row;
    }

    $stmt->close();
    $conn->close();

    // Return JSON response
    echo json_encode(['success' => true, 'lots' => $lots]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid HOA parameter']);
}
?>
