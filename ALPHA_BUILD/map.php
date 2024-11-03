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
        /* Custom popup styling */
        .leaflet-popup-content-wrapper {
            overflow: visible;
        }
        .popup-content {
            font-size: 14px;
        }
        .more-info {
            display: none; /* Hide extra info initially */
        }
        .reveal-button {
            color: black;
           
            cursor: pointer;
        }
        .lot-label {
            font-size: 14px;
            font-weight: bold;
            color: darkred;
            background: rgba(255, 255, 255, 0.8);
            padding: 2px 6px;
            border-radius: 4px;
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
        var geoJsonLayers = [];
        var dataHierarchy = {};

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        function populateSelectors() {
            fetch('fetch_unique_values.php')
                .then(response => response.json())
                .then(data => {
                    dataHierarchy = data;
                    Object.keys(data).forEach(district => {
                        document.getElementById('district').innerHTML += `<option value="${district}">${district}</option>`;
                    });
                    document.getElementById('district').addEventListener('change', updateBarangayOptions);
                })
                .catch(error => console.error("Error fetching unique values: ", error));
        }

        function updateBarangayOptions() {
            const districtSelect = document.getElementById('district');
            const barangaySelect = document.getElementById('barangay');
            const hoaSelect = document.getElementById('hoa');

            barangaySelect.innerHTML = '<option value="">All</option>';
            hoaSelect.innerHTML = '<option value="">All</option>';
            hoaSelect.disabled = true;

            if (districtSelect.value) {
                const barangays = dataHierarchy[districtSelect.value].barangays;
                Object.keys(barangays).forEach(barangay => {
                    barangaySelect.innerHTML += `<option value="${barangay}">${barangay}</option>`;
                });
                barangaySelect.disabled = false;
            } else {
                barangaySelect.disabled = true;
            }

            barangaySelect.addEventListener('change', updateHoaOptions);
        }

        function updateHoaOptions() {
            const barangaySelect = document.getElementById('barangay');
            const hoaSelect = document.getElementById('hoa');

            hoaSelect.innerHTML = '<option value="">All</option>';
            hoaSelect.disabled = true;

            if (barangaySelect.value) {
                const hoas = dataHierarchy[document.getElementById('district').value].barangays[barangaySelect.value].hoas;
                hoas.forEach(hoa => {
                    hoaSelect.innerHTML += `<option value="${hoa}">${hoa}</option>`;
                });
                hoaSelect.disabled = false;
            } else {
                hoaSelect.disabled = true;
            }
        }

        function loadPolygons() {
    const district = document.getElementById('district').value;
    const barangay = document.getElementById('barangay').value;
    const hoa = document.getElementById('hoa').value;

    const query = `fetch_polygons.php?district=${district}&barangay=${barangay}&hoa=${hoa}`;

    fetch(query)
        .then(response => response.json())
        .then(data => {
            geoJsonLayers.forEach(layer => map.removeLayer(layer));
            geoJsonLayers = [];

            data.forEach(area => {
                let geojson = JSON.parse(area.geojson);
                let layer = L.geoJSON(geojson, {
                    onEachFeature: function (feature, layer) {
                        const center = layer.getBounds().getCenter();
                        const coordinates = `${center.lat.toFixed(5)}, ${center.lng.toFixed(5)}`;
                        let popupContent = `
                            <div class="popup-content">
                                <strong>Lot No:</strong> ${area.lot_no}<br>
                                <strong>Center Coordinates:</strong> 
                                <span id="address-${area.id}">${coordinates}</span><br>
                                <button id="toggleAddress-${area.id}" onclick="toggleAddress(${center.lat}, ${center.lng}, 'address-${area.id}', this)">Convert to Address</button>
                                <button class="reveal-button" onclick="revealMore(this)">More Details</button>

                                <div class="more-info" style="display: none;">
                                    <strong>District:</strong> ${area.district}<br>
                                    <strong>Barangay:</strong> ${area.barangay}<br>
                                    <strong>ID:</strong> ${area.id}<br>
                                    <strong>HOA:</strong> ${area.hoa}<br>
                                    <div id="members-${area.id}"></div> <!-- Members Div -->
                                </div>
                            </div>
                        `;
                        layer.bindPopup(popupContent);
                        layer.on('popupopen', () => fetchMembers(area.lot_no, area.hoa, area.id));
                    }
                }).addTo(map);

                geoJsonLayers.push(layer);
            });
        })
        .catch(error => console.error("Error fetching polygons: ", error));
}

function fetchMembers(lotNo, hoa, areaId) {
    fetch(`get_member_info.php?lot_no=${lotNo}&hoa=${hoa}`)
        .then(response => response.json())
        .then(members => {
            const membersDiv = document.getElementById(`members-${areaId}`);
            if (members) { // Ensure membersDiv exists
                membersDiv.innerHTML = ''; // Clear previous members info
                if (members.length > 0) {
                    // Find the first member matching the lot_no
                    const member = members.find(member => member.lot_no === lotNo);
                    if (member) {
                        membersDiv.innerHTML = `
                            <div class="member-details">
                                <strong>Name:</strong> ${member.name}<br>
                                <strong>Role:</strong> ${member.role}<br>
                                <strong>Phone:</strong> ${member.phone_number}<br>
                                <hr>
                            </div>
                        `;
                    } else {
                        membersDiv.innerHTML = '<strong>No matching member found.</strong>';
                    }
                } else {
                    membersDiv.innerHTML = '<strong>No members found.</strong>';
                }
            } else {
                console.error("Members div not found for area ID:", areaId);
            }
        })
        .catch(error => console.error("Error fetching members: ", error));
}




function revealMore(element) {
    const moreInfo = element.parentElement.querySelector('.more-info'); // Get the sibling more-info div
    moreInfo.style.display = moreInfo.style.display === 'none' ? 'block' : 'none';
    element.textContent = moreInfo.style.display === 'block' ? 'Hide Details' : 'More Details'; // Update button text
}


        function convertToAddress(lat, lng, elementId) {
            const url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const address = data.display_name || 'No address found';
                    document.getElementById(elementId).textContent = address;
                })
                .catch(error => console.error("Error fetching address: ", error));
        }

        // Function to toggle between converting to address and back to coordinates
function toggleAddress(lat, lng, elementId, button) {
    const addressSpan = document.getElementById(elementId);

    if (button.textContent === "Convert to Address") {
        const url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const address = data.display_name || 'No address found';
                addressSpan.textContent = address;
                button.textContent = "Back to Coordinates";
            })
            .catch(error => console.error("Error fetching address: ", error));
    } else {
        const coordinates = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        addressSpan.textContent = coordinates;
        button.textContent = "Convert to Address";
    }
}


        document.getElementById('district').addEventListener('change', function() {
            updateBarangayOptions();
            loadPolygons();
        });

        document.getElementById('barangay').addEventListener('change', function() {
            updateHoaOptions();
            loadPolygons();
        });

        document.getElementById('hoa').addEventListener('change', loadPolygons);

        populateSelectors();
        loadPolygons();
    </script>
</body>
</html>
