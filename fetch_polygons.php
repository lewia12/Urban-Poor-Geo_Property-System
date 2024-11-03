<?php
// fetch_polygons.php
header('Content-Type: application/json');

// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your actual password
$dbname = "hoa_records";

try {
    // Create connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the SQL query with filters based on the selected options
    $query = "SELECT id, district, barangay, hoa, geojson, lot_no FROM polygons WHERE 1=1";
    $filters = [];

    // Add filters based on query parameters
    if (!empty($_GET['district'])) {
        $district = $_GET['district'];
        $query .= " AND district = :district";
        $filters[':district'] = $district;
    }

    if (!empty($_GET['barangay'])) {
        $barangay = $_GET['barangay'];
        $query .= " AND barangay = :barangay";
        $filters[':barangay'] = $barangay;
    }

    if (!empty($_GET['hoa'])) {
        $hoa = $_GET['hoa'];
        $query .= " AND hoa = :hoa";
        $filters[':hoa'] = $hoa;
    }

    // Prepare and execute the statement
    $stmt = $conn->prepare($query);
    $stmt->execute($filters);

    // Fetch all rows as an associative array
    $polygons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output as JSON
    echo json_encode($polygons);
} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn = null;
?>
