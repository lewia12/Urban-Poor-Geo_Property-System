<!DOCTYPE html>
<html>
<head>
    <title>Leaflet Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <style>
        #map {
            height: 600px; /* Adjust height as needed */
            width: 100%; /* Ensure the map takes up full width */
            margin-top: 50px; /* Adjust the margin to ensure map does not overlap with the navbar */
        }

        /* Navbar styling */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black background */
            color: white;
            padding: 10px;
            display: flex;
            justify-content: flex-start; /* Align items to the left */
            align-items: center;
            z-index: 1000; /* Ensure the navbar stays on top */
        }


        .navbar ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .navbar li {
            margin: 0 15px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
        }

        .navbar a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <ul>
            <li><a href="index.php">Home</a></li>
            <!-- Add more buttons here as needed -->
        </ul>
    </div>

    <!-- Map container -->
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Map initialization
        var map = L.map('map').setView([12.0675, 124.5975], 16); // Adjust coordinates and zoom level

        // Basemap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // HOA areas with adjusted coordinates (for demonstration purposes only)
        var hoaArea1 = L.polygon([
            [12.07054, 124.59336],
            [12.06944, 124.59503],
            [12.07002, 124.59543],
            [12.07049, 124.59484],
            [12.07170, 124.59573],
            [12.07286, 124.59786],
            [12.07243, 124.59966],
            [12.07487, 124.60174],
            [12.07639, 124.60108],
            [12.07635, 124.60014],
            [12.07806, 124.59981],
            [12.07806, 124.59943],
            [12.07529, 124.59881],
            [12.07474, 124.59818],
            [12.07348, 124.59729],
            [12.07401, 124.59564],
            [12.07074, 124.59347]
        ], {
            color: 'red',
            fillColor: 'red',
            fillOpacity: 0.2
        }).addTo(map).bindPopup('<b>Hamorawon HOA</b>');

        var hoaArea2 = L.polygon([
            [12.05, 124.58],
            [12.06, 124.59],
            [12.055, 124.60]
        ], {
            color: 'blue',
            fillColor: 'blue',
            fillOpacity: 0.2
        }).addTo(map).bindPopup('<b>HOA Area 2</b><br>This is HOA Area 2.');

        // Marker
        L.marker([12.0675, 124.5975]).addTo(map)
            .bindPopup('<b>Central Location</b><br>This is the central location.')
            .openPopup();
    </script>
</body>
</html>
