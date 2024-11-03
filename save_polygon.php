<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hoa_records";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

$district = $conn->real_escape_string($data['district']);
$barangay = $conn->real_escape_string($data['barangay']);
$hoa = $conn->real_escape_string($data['hoa']);
$lot_no = $conn->real_escape_string($data['lot_no']);
$polygons = $data['polygons'];

$response = ['success' => true];
foreach ($polygons as $polygon) {
    $geojson = json_encode($polygon);
    $sql = "INSERT INTO polygons (district, barangay, hoa, lot_no, geojson) VALUES ('$district', '$barangay', '$hoa', '$lot_no', '$geojson')";
    if (!$conn->query($sql)) {
        $response = ['success' => false, 'message' => $conn->error];
        break;
    }
}

$conn->close();

echo json_encode($response);
?>
