<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="homestyle.css">

    <style>
        body {
            font-family: Arial, sans-serif;
        }
        #map {
            height: 600px;
            width: 100%;
            margin-top: 20px;
        }
        .selector {
            margin: 20px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><a href="#">Alpha Build</a></div>
            <ul class="nav-links">
                <li><a href="records.php">Records</a></li>
                <li><a href="lot_editor.html">Editor</a></li>
            </ul>
        </nav>
    </header>

    <div class="selector">
        <label for="district">Select District:</label>
        <select id="district">
            <option value="">All</option>
        </select>

        <label for="barangay">Select Barangay:</label>
        <select id="barangay" disabled>
            <option value="">All</option>
        </select>

        <label for="hoa">Select HOA:</label>
        <select id="hoa" disabled>
            <option value="">All</option>
        </select>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([12.0675, 124.5975], 16);
        var geoJsonLayers = []; // Store geoJson layers for clearing
        var dataHierarchy = {}; // Store fetched data hierarchy

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Function to populate selectors with unique values from the database
        function populateSelectors() {
            fetch('fetch_unique_values.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error("Error loading unique values: ", data.error);
                        return;
                    }

                    dataHierarchy = data; // Store fetched data for later use

                    // Populate district selector
                    Object.keys(data).forEach(district => {
                        document.getElementById('district').innerHTML += `<option value="${district}">${district}</option>`;
                    });

                    // Enable district selector change event
                    document.getElementById('district').addEventListener('change', updateBarangayOptions);
                })
                .catch(error => console.error("Error fetching unique values: ", error));
        }

        // Update barangay options based on selected district
        function updateBarangayOptions() {
            const districtSelect = document.getElementById('district');
            const barangaySelect = document.getElementById('barangay');
            const hoaSelect = document.getElementById('hoa');

            // Clear previous options
            barangaySelect.innerHTML = '<option value="">All</option>';
            hoaSelect.innerHTML = '<option value="">All</option>';
            hoaSelect.disabled = true; // Disable HOA selector until a barangay is selected

            if (districtSelect.value) {
                const barangays = dataHierarchy[districtSelect.value].barangays;

                // Populate barangay selector
                Object.keys(barangays).forEach(barangay => {
                    barangaySelect.innerHTML += `<option value="${barangay}">${barangay}</option>`;
                });
                barangaySelect.disabled = false; // Enable barangay selector
            } else {
                barangaySelect.disabled = true; // Disable barangay selector if no district is selected
            }

            // Enable barangay selector change event
            barangaySelect.addEventListener('change', updateHoaOptions);
        }

        // Update HOA options based on selected barangay
        function updateHoaOptions() {
            const barangaySelect = document.getElementById('barangay');
            const hoaSelect = document.getElementById('hoa');

            // Clear previous options
            hoaSelect.innerHTML = '<option value="">All</option>';
            hoaSelect.disabled = true; // Disable HOA selector until a barangay is selected

            if (barangaySelect.value) {
                const hoas = dataHierarchy[document.getElementById('district').value].barangays[barangaySelect.value].hoas;

                // Populate HOA selector
                hoas.forEach(hoa => {
                    hoaSelect.innerHTML += `<option value="${hoa}">${hoa}</option>`;
                });
                hoaSelect.disabled = false; // Enable HOA selector
            } else {
                hoaSelect.disabled = true; // Disable HOA selector if no barangay is selected
            }
        }

        // Load polygons from database based on selected values
        function loadPolygons() {
            const district = document.getElementById('district').value;
            const barangay = document.getElementById('barangay').value;
            const hoa = document.getElementById('hoa').value;

            // Create a query string with selected filters
            const query = `fetch_polygons.php?district=${district}&barangay=${barangay}&hoa=${hoa}`;

            fetch(query)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error("Error loading polygons: ", data.error);
                        return;
                    }

                    // Clear existing layers
                    geoJsonLayers.forEach(layer => map.removeLayer(layer));
                    geoJsonLayers = []; // Reset array

                    // Filter and add polygons based on selected values
                    data.forEach(area => {
                        let geojson = JSON.parse(area.geojson); // Parse GeoJSON data

                        // Create a GeoJSON layer
                        let layer = L.geoJSON(geojson, {
                            onEachFeature: function (feature, layer) {
                                // Bind a popup to each polygon
                                layer.bindPopup(`
                                    <strong>District:</strong> ${area.district}<br>
                                    <strong>Barangay:</strong> ${area.barangay}<br>
                                    <strong>ID:</strong> ${area.id}<br>
                                    <strong>HOA:</strong> ${area.hoa}
                                `);
                            }
                        }).addTo(map); // Add GeoJSON layer to the map

                        geoJsonLayers.push(layer); // Store layer for clearing
                    });
                })
                .catch(error => console.error("Error fetching polygons: ", error));
        }

        // Event listeners for selectors
        document.getElementById('district').addEventListener('change', function() {
            updateBarangayOptions();
            loadPolygons(); // Load polygons on district change
        });

        document.getElementById('barangay').addEventListener('change', function() {
            updateHoaOptions();
            loadPolygons(); // Load polygons on barangay change
        });

        document.getElementById('hoa').addEventListener('change', loadPolygons); // Load polygons on HOA change

        // Initial population of selectors and loading of polygons
        populateSelectors();
        loadPolygons();
    </script>
</body>
</html>
