<?php
session_start();
include 'dbconnection.php';

// Handle logout request
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Check if the user is logged in
$login_required = false;
$username = ''; // Initialize username

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id']; // Assuming user ID is stored in the session

        // Fetch the username and last login time
        $sql = "SELECT username, last_login_time FROM users WHERE id = '$userId'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $username = $user['username'];
            $last_login_time = $user['last_login_time'];

            // Update last_loggedin
            $updateSql = "UPDATE users SET last_login_time = NOW() WHERE id = '$userId'";
            $conn->query($updateSql); // Execute the query to update the timestamp
        } else {
            // Handle case where user ID is valid but no user is found
            $_SESSION['loggedin'] = false;
            $login_required = true;
        }
    } else {
        // Handle case where user_id is not set
        $_SESSION['loggedin'] = false;
        $login_required = true;
    }
} else {
    $login_required = true;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Update last_login_time
        $userId = $user['id'];
        $updateSql = "UPDATE users SET last_login_time = NOW() WHERE id = '$userId'";
        $conn->query($updateSql); // Execute the query to update the timestamp

        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        header('Location: index.php');
        exit();
    } else {
        $login_error = "Invalid username or password";
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
    <?php if ($login_required): ?>
        <div id="loginModal" class="modal">
            <div class="modal-content">
                <form method="post" action="">
                    <h2>Login</h2>
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                    <?php if (isset($login_error)): ?>
                        <p class="error"><?= htmlspecialchars($login_error) ?></p>
                    <?php endif; ?>
                    <button type="submit">Login</button>
                </form>
            </div>
        </div>
        <script>
            document.getElementById('loginModal').style.display = 'block';
        </script>
    <?php else: ?>
        <header>
            <nav class="navbar">
                <div class="logo">
                    <a href="#">Alpha Build</a>
                </div>
                <ul class="nav-links">
                    <li><a href="records.php">Records</a></li>
                    <li><a href="map.php">Map</a></li>
                    <li class="user-info">Welcome, <?= htmlspecialchars($username) ?> <a href="?logout">Logout</a></li>
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

            // Secret admin redirect logic
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Shift' && event.shiftKey) {
                    if (!shiftMDown) {
                        shiftMDown = true;
                        shiftMStartTime = Date.now();
                    }
                } else if (event.key === 'M' && event.shiftKey && shiftMDown) {
                    if (Date.now() - shiftMStartTime >= 10000) { // 10 seconds in milliseconds
                        let adminCode = prompt('Enter the admin code:');
                        if (adminCode === 'A1B2C3') {
                            window.location.href = 'admin.php';
                        } else {
                            alert('Incorrect code');
                        }
                        shiftMDown = false;
                    }
                } else {
                    shiftMDown = false;
                }
            });

            let shiftMDown = false;
            let shiftMStartTime;
        </script>
    <?php endif; ?>
</body>
</html>
