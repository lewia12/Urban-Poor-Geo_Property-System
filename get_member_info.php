<?php
// get_member_info.php
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

    // Check if lot_no and hoa are set in the GET request
    if (isset($_GET['lot_no']) && isset($_GET['hoa'])) {
        $lot_no = $_GET['lot_no'];
        $hoa = $_GET['hoa'];

        // Prepare the SQL query to fetch members based on lot_no and hoa
        $query = "SELECT * FROM member WHERE lot_no = :lot_no AND hoa = :hoa";
        $stmt = $conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':lot_no', $lot_no, PDO::PARAM_INT);
        $stmt->bindParam(':hoa', $hoa, PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Fetch all rows as an associative array
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Output as JSON
        echo json_encode($members);
    } else {
        // If lot_no or hoa is not set, return an error message
        echo json_encode(["error" => "lot_no and hoa parameters are required."]);
    }
} catch(PDOException $e) {
    // Handle any connection or query errors
    echo json_encode(["error" => $e->getMessage()]);
}

// Close the database connection
$conn = null;
?>
