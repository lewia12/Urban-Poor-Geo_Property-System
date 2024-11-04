<?php
// Function to load environment variables from a .env file
function loadEnv($filePath) {
    if (file_exists($filePath)) {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // Skip comments
            }
            putenv(trim($line)); // Load the variable into the environment
        }
    } else {
        die("Error: .env file not found at: " . htmlspecialchars($filePath));
    }
}

// Load environment variables
loadEnv(__DIR__ . '/db.env'); // Adjust the path if necessary

// Database credentials
$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS'); // This may be empty
$db_name = getenv('DB_NAME');

// Check if the required environment variables are set
if (!$db_host || !$db_user || !$db_name) {
    die("Error: Missing database configuration in environment variables.");
}

// Create a connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
