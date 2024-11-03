<?php
// fetch_unique_values.php
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

    // Prepare SQL queries to get unique values
    $stmt = $conn->query("SELECT DISTINCT district, barangay, hoa FROM polygons");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];

    foreach ($data as $row) {
        $district = $row['district'];
        $barangay = $row['barangay'];
        $hoa = $row['hoa'];

        // Create the district structure
        if (!isset($result[$district])) {
            $result[$district] = [
                'barangays' => []
            ];
        }

        // Create the barangay structure
        if (!isset($result[$district]['barangays'][$barangay])) {
            $result[$district]['barangays'][$barangay] = [
                'hoas' => []
            ];
        }

        // Add HOA
        if (!in_array($hoa, $result[$district]['barangays'][$barangay]['hoas'])) {
            $result[$district]['barangays'][$barangay]['hoas'][] = $hoa;
        }
    }

    // Output as JSON
    echo json_encode($result);
} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn = null;
?>
