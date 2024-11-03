<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOA Records</title>
    <link rel="stylesheet" href="recordstyle.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="#">Alpha Build</a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="map.php">Map</a></li>
            </ul>
        </nav>
    </header>
    <div class="records-container">
        <div class="records-list">
            <input type="text" id="search-bar" placeholder="Search HOAs" class="search-bar" oninput="filterHOAs()">
            <h2>Home Owners Associations</h2>
            <ul id="hoa-list">
                <!-- HOAs will be populated here by JavaScript -->
            </ul>
        </div>
        <div class="record-details">
            <!-- Record details will be displayed here when an HOA is selected -->
        </div>
    </div>

    <script>
        const hoaList = document.getElementById('hoa-list');
        let allHOAs = []; // Array to hold all fetched HOAs

        // Function to fetch HOAs from the server
        function fetchHOAs() {
            fetch('fetch_hoas.php') // Create a new PHP file for fetching HOAs
                .then(response => response.json())
                .then(data => {
                    allHOAs = data; // Store fetched HOAs in the array
                    displayHOAs(data); // Display fetched HOAs
                })
                .catch(error => console.error("Error fetching HOAs:", error));
        }

        // Function to display HOAs
        function displayHOAs(data) {
            hoaList.innerHTML = ''; // Clear the existing list
            if (data.length > 0) {
                data.forEach(hoa => {
                    const li = document.createElement('li');
                    const button = document.createElement('button'); // Create a button element
                    button.className = 'hoa-button'; // Assign a class
                    button.innerText = hoa; // Set the button text
                    button.onclick = () => viewHOADetails(hoa); // Set click handler to redirect
                    li.appendChild(button); // Append the button to the list item
                    hoaList.appendChild(li); // Append the list item to the list
                });
            } else {
                hoaList.innerHTML = '<li>No HOAs found</li>';
            }
        }

        // Function to filter HOAs based on search input
        function filterHOAs() {
            const query = document.getElementById('search-bar').value.toLowerCase();
            const filteredHOAs = allHOAs.filter(hoa => hoa.toLowerCase().includes(query));
            displayHOAs(filteredHOAs);
        }

        // Function to view HOA details
        function viewHOADetails(hoa) {
            // Redirect to hoarecords.php with the selected HOA as a query parameter
            window.location.href = `hoarecords.php?hoa=${encodeURIComponent(hoa)}`;
        }

        // Fetch HOAs every 10 seconds
        setInterval(fetchHOAs, 10000);
        // Initial fetch on page load
        fetchHOAs();
    </script>
</body>
</html>
