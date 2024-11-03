<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lot Plotter</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
    <style>
        #map { height: 600px; width: 100%; }
        body { font-family: Arial, sans-serif; margin: 20px; }
    </style>
</head>
<body>
    <h1>Lot Plotter</h1>
    <form id="plotForm">
        <label>Starting Latitude: <input type="number" step="any" id="startLat" required></label><br>
        <label>Starting Longitude: <input type="number" step="any" id="startLng" required></label><br>
        <label>Number of Lines: <input type="number" id="lineCount" value="1" required></label><br>
        
        <div id="lineInputs"></div>
        <button type="submit">Plot Lot</button>
    </form>

    <div id="map"></div>

    <script>
        const form = document.getElementById('plotForm');
        const lineCountInput = document.getElementById('lineCount');
        const lineInputs = document.getElementById('lineInputs');

        // Dynamically add input fields for metes and bounds description
        lineCountInput.addEventListener('input', () => {
            let count = lineCountInput.value;
            lineInputs.innerHTML = ''; // Clear previous inputs
            for (let i = 1; i <= count; i++) {
                lineInputs.innerHTML += `
                    <label>Line ${i} NS: 
                        <select id="ns_${i}" required>
                            <option value="N">N</option>
                            <option value="S">S</option>
                        </select>
                    </label>
                    <label>Degrees: <input type="number" step="any" id="deg_${i}" required></label>
                    <label>Minutes: <input type="number" step="any" id="min_${i}" required></label>
                    <label>EW: 
                        <select id="ew_${i}" required>
                            <option value="E">E</option>
                            <option value="W">W</option>
                        </select>
                    </label>
                    <label>Distance (m): <input type="number" step="any" id="distance_${i}" required></label><br>
                `;
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const startLat = parseFloat(document.getElementById('startLat').value);
            const startLng = parseFloat(document.getElementById('startLng').value);
            const lines = [];

            // Gather technical description for each line
            for (let i = 1; i <= lineCountInput.value; i++) {
                lines.push({
                    ns: document.getElementById(`ns_${i}`).value,
                    deg: parseFloat(document.getElementById(`deg_${i}`).value),
                    min: parseFloat(document.getElementById(`min_${i}`).value),
                    ew: document.getElementById(`ew_${i}`).value,
                    distance: parseFloat(document.getElementById(`distance_${i}`).value)
                });
            }

            // Plot the lot on the map
            const map = L.map('map').setView([startLat, startLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            // Calculate coordinates based on technical description
            let latLngs = [[startLat, startLng]];
            let currentLat = startLat;
            let currentLng = startLng;

            lines.forEach(line => {
                // Convert DMS (Degrees and Minutes) to decimal degrees
                const angleInDecimal = line.deg + (line.min / 60);
                const distance = line.distance; // Distance in meters

                // Convert angle to radians
                const angleInRadians = (angleInDecimal * Math.PI) / 180;

                // Calculate new latitude and longitude
                const deltaLat = distance * Math.cos(angleInRadians) / 111320; // Rough conversion (m to degrees)
                const deltaLng = distance * Math.sin(angleInRadians) / (111320 * Math.cos((currentLat * Math.PI) / 180)); // Rough conversion (m to degrees)

                // Adjust currentLat and currentLng based on NS and EW
                currentLat += (line.ns === 'N' ? deltaLat : -deltaLat);
                currentLng += (line.ew === 'E' ? deltaLng : -deltaLng);

                latLngs.push([currentLat, currentLng]);
            });

            // Draw the polyline
            const polyline = L.polyline(latLngs, { color: 'blue' }).addTo(map);
            map.fitBounds(polyline.getBounds());
        });
    </script>
</body>
</html>
