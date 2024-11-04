<?php
session_start();
include 'dbconnection.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect to login page
    exit();
}

// Initialize username and role
$username = '';
$role = ''; // Initialize the role variable

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id']; // Assuming user ID is stored in the session

    // Fetch the username, last login time, and role
    $sql = "SELECT username, last_login_time, role FROM users WHERE id = '$userId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $username = $user['username'];
        $last_login_time = $user['last_login_time'];
        $role = $user['role']; // Store the user's role in the variable

        // Update last_loggedin
        $updateSql = "UPDATE users SET last_login_time = NOW() WHERE id = '$userId'";
        $conn->query($updateSql); // Execute the query to update the timestamp
    } else {
        // Handle case where user ID is valid but no user is found
        $_SESSION['loggedin'] = false;
        header('Location: login.php'); // Redirect to login page
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="homestyle.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
            <img src="chdo.png" alt="City Housing Logo" style="width: 50px; height: auto; border-radius: 70%;">
            </div>
            <ul class="nav-links">
                <li><a href="records.php">Records</a></li>
                <li><a href="map.php">Map</a></li>
                <?php if ($role === 'Admin'): // Only show the admin link if the user is an admin ?>
                    <li><a href="admin.php">Admin Panel</a></li>
                <?php endif; ?>
                <li class="user-info">Welcome, <?= htmlspecialchars($username) ?> <a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero">
        <div id="mapid"></div>
    </section>

    <script>
        var map = L.map('mapid', {
            zoomControl: false,
            dragging: false, 
            touchZoom: false,
            doubleClickZoom: false,
            scrollWheelZoom: false,
            boxZoom: false,
            keyboard: false,
            tap: false
        }).setView([12.0675, 124.5975], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var pathPoints = [
            [12.07, 124.6],
            [12.0696, 124.5873],
            [12.0655, 124.6093]
        ];

        var currentPointIndex = 0;

        function autoPan() {
            if (currentPointIndex < pathPoints.length) {
                map.panTo(pathPoints[currentPointIndex]);
                currentPointIndex++;
            } else {
                currentPointIndex = 0;
            }

            setTimeout(autoPan, 4000);
        }

        autoPan();

        let shiftMDown = false;
        let shiftMStartTime = 0;
        let disableKeyboard = false;

        // Secret admin redirect logic
        document.addEventListener('keydown', function(event) {
            if (disableKeyboard) {
                // Block all key presses if the keyboard is disabled
                event.preventDefault();
                return;
            }

            if (event.key === 'Shift' && event.shiftKey) {
                if (!shiftMDown) {
                    shiftMDown = true;
                    shiftMStartTime = Date.now();
                }
            } else if (event.key === 'M' && event.shiftKey && shiftMDown) {
                if (Date.now() - shiftMStartTime >= 10000) { // 10 seconds in milliseconds
                    shiftMDown = false;
                    disableKeyboard = true;

                    // Show the prompt immediately
                    let adminCode = prompt('Enter the admin code:');
                    if (adminCode === 'A1B2C3') {
                        window.location.href = 'admin.php';
                    } else {
                        alert('Incorrect code');
                    }

                    // Re-enable keyboard input after 5 seconds
                    setTimeout(() => {
                        disableKeyboard = false; // Re-enable keyboard input after 5 seconds
                    }, 5000); // 5 seconds delay
                }
            } else {
                shiftMDown = false;
            }
        });

    </script>
</body>
</html>
