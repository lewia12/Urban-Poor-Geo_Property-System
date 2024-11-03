<?php
require 'dbconnection.php'; // Include your database connection

$hoa_id = isset($_GET['hoa_id']) ? intval($_GET['hoa_id']) : 0;

// Fetch member information based on the HOA ID
$query = "SELECT * FROM members WHERE hoa_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $hoa_id);
$stmt->execute();
$result = $stmt->get_result();
$members = [];
while ($row = $result->fetch_assoc()) {
    $members[$row['member_id']] = $row; // Store members by member_id
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOA Area Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        #map {
            height: 600px;
            width: 100%; /* Full width for the map */
            margin-top: 50px;
        }
        .popup-content {
            max-width: 300px; /* Set max width for popup */
        }
        .close {
            cursor: pointer;
            color: #aaa;
            float: right;
            font-size: 20px;
        }
        .close:hover {
            color: black;
        }
        .editable {
            display: none; /* Initially hide editable inputs */
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
       var map = L.map('map', {
        maxZoom: 18 // Set your desired maximum zoom level here
}).setView([12.077887, 124.599492], 17); // Initial view

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Define HOA lots with associated member IDs
        var lots = [
    {
        coords: [
            [12.077971, 124.599495], // Top-left
            [12.077887, 124.599492], // Top-right
            [12.077890, 124.599416], // Bottom-right
            [12.077971, 124.599495]  // Closing the square (should match the first coordinate)
        ],
        color: 'red',
        fillColor: 'red',
        fillOpacity: 0.2,
        member_ids: [1920392] // Example member IDs
    },
    {
        coords: [
            [12.077800, 124.599476], // Top-left
            [12.077887, 124.599492], // Top-right
            [12.077890, 124.599416], // Bottom-right
            [12.077794, 124.599403], // Bottom-left
            [12.077800, 124.599476]  // Closing the polygon (should match the first coordinate)
        ],
        color: 'red',
        fillColor: 'red',
        fillOpacity: 0.2,
        member_ids: [1920323] // Example member IDs
    }
    // Add more lots as needed
];


      

        // Create polygons for lots
        lots.forEach(lot => {
            var polygon = L.polygon(lot.coords, {
                color: lot.color,
                fillColor: lot.fillColor,
                fillOpacity: lot.fillOpacity
            }).addTo(map).on('click', function(e) {
                showMemberInfo(lot.member_ids, e); // Pass the event to showMemberInfo
            });
        });

        // Display Member Information in a popup
        function showMemberInfo(member_ids, e) {
            var member = <?= json_encode($members); ?>[member_ids[0]]; // Access the first member data

            var popupContent = `
                <div class="popup-content">
                    <span class="close" onclick="closePopup()">×</span>
                    <h2>Member Information</h2>
                    <div>
                        <label for="name">Name:</label>
                        <span id="name-display">${member.name}</span>
                        <input type="text" id="name" class="editable" value="${member.name}"><br>
                    </div>
                    <div>
                        <label for="address">Address:</label>
                        <span id="address-display">${member.address}</span>
                        <input type="text" id="address" class="editable" value="${member.address}"><br>
                    </div>
                    <div>
                        <label for="phone_number">Phone Number:</label>
                        <span id="phone_number-display">${member.phone_number}</span>
                        <input type="text" id="phone_number" class="editable" value="${member.phone_number}"><br>
                    </div>
                    <div>
                        <label for="email">Email:</label>
                        <span id="email-display">${member.email}</span>
                        <input type="text" id="email" class="editable" value="${member.email}"><br>
                    </div>
                    <div>
                        <label for="membership_date">Membership Date:</label>
                        <span id="membership_date-display">${member.membership_date}</span>
                        <input type="text" id="membership_date" class="editable" value="${member.membership_date}"><br>
                    </div>
                    <div>
                        <label for="status">Status:</label>
                        <span id="status-display">${member.status}</span>
                        <input type="text" id="status" class="editable" value="${member.status}"><br>
                    </div>
                    <div>
                        <label for="role">Role:</label>
                        <span id="role-display">${member.role}</span>
                        <input type="text" id="role" class="editable" value="${member.role}"><br>
                    </div>
                    <div>
                        <label for="notes">Notes:</label>
                        <span id="notes-display">${member.notes}</span>
                        <textarea id="notes" class="editable">${member.notes}</textarea><br>
                    </div>
                    <button onclick="toggleEdit()">Edit</button>
                    <button onclick="saveMemberInfo(${member.member_id})" style="display:none;" id="save-button">Save</button>
                </div>
            `;

            // Create a popup and set its content at the clicked location
            var popup = L.popup()
                .setLatLng(e.latlng) // Use the latitude and longitude of the click event
                .setContent(popupContent)
                .openOn(map);
        }

        // Toggle between viewing and editing member info
function toggleEdit() {
    const editableFields = document.querySelectorAll('.editable');
    const displayFields = document.querySelectorAll('[id$="-display"]');
    const saveButton = document.getElementById('save-button');

    let isEditing = editableFields[0].style.display === 'block'; // Check if currently in editing mode

    // Toggle the display of editable fields and static display fields
    editableFields.forEach(field => {
        field.style.display = isEditing ? 'none' : 'block';
    });

    displayFields.forEach(field => {
        field.style.display = isEditing ? 'inline' : 'none';
    });

    // Show or hide the save button
    saveButton.style.display = isEditing ? 'none' : 'inline';
}


       function saveMemberInfo(member_id) {
    var updatedMember = {
        name: document.getElementById('name').value,
        address: document.getElementById('address').value,
        phone_number: document.getElementById('phone_number').value,
        email: document.getElementById('email').value,
        membership_date: document.getElementById('membership_date').value,
        status: document.getElementById('status').value,
        role: document.getElementById('role').value,
        notes: document.getElementById('notes').value
    };

    fetch('update_member.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ member_id: member_id, ...updatedMember })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Member information updated successfully!');
            // Update the popup with the new values
            updatePopupContent(updatedMember);
        } else {
            alert('Error updating member information.');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Function to update the popup content
function updatePopupContent(member) {
    var popupContent = `
        <div class="popup-content">
            <span class="close" onclick="closePopup()">×</span>
            <h2>Member Information</h2>
            <label for="name">Name:</label>
            <input type="text" id="name" value="${member.name}" readonly><br>
            <label for="address">Address:</label>
            <input type="text" id="address" value="${member.address}" readonly><br>
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" value="${member.phone_number}" readonly><br>
            <label for="email">Email:</label>
            <input type="text" id="email" value="${member.email}" readonly><br>
            <label for="membership_date">Membership Date:</label>
            <input type="text" id="membership_date" value="${member.membership_date}" readonly><br>
            <label for="status">Status:</label>
            <input type="text" id="status" value="${member.status}" readonly><br>
            <label for="role">Role:</label>
            <input type="text" id="role" value="${member.role}" readonly><br>
            <label for="notes">Notes:</label>
            <textarea id="notes" readonly>${member.notes}</textarea><br>
            <button onclick="editMemberInfo()">Edit</button>
        </div>
    `;

    // Update the existing popup content
    var popup = L.popup()
        .setLatLng(map.getCenter()) // Optionally, keep the popup at the same position
        .setContent(popupContent)
        .openOn(map);
}


        function closePopup() {
            map.closePopup(); // Close the current popup
        }
    </script>
</body>
</html>
