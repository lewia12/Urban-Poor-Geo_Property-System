<?php
// save_polygon.php

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input data
if (!isset($data['district']) || !isset($data['barangay']) || !isset($data['hoa']) || !isset($data['polygons'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=gis_system', 'root', ''); // Using 'root' as the username
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare insert statement
    $stmt = $pdo->prepare("INSERT INTO polygons (district, barangay, hoa, geojson) VALUES (?, ?, ?, ?)");

    foreach ($data['polygons'] as $polygon) {
        $stmt->execute([$data['district'], $data['barangay'], $data['hoa'], json_encode($polygon)]);
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
