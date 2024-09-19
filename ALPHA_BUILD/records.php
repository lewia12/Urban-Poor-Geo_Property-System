<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOA Records</title>
    <link rel="stylesheet" href="recordstyle.css">
</head>
<body>
    <div class="menu-bar">
        <div class="menu-icon">
            <!-- You can add menu icon content here if needed -->
        </div>
        <h1>Records</h1>
        <div>
        <button class="menu-button" onclick="window.location.href='index.php'">Home</button>
        <button class="menu-button" onclick="window.location.href='blacklist.php'">Blacklist</button>

        </div>
    </div>

    <div class="records-container">
        <div class="records-list">
            <input type="text" placeholder="Search" class="search-bar">
            <h2>Home Owners Associations</h2>
            <ul>
                <?php
                // Include the database connection file
                include 'dbconnection.php';

                // SQL query to fetch HOA names
                $sql = "SELECT id, name FROM hoas";  // Assuming there is an 'id' column
                $result = $conn->query($sql);

                // Check if there are results and output each HOA name as a clickable link
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<li><a href="hoarecords.php?id=' . htmlspecialchars($row["id"]) . '">' . htmlspecialchars($row["name"]) . '</a></li>';
                    }
                } else {
                    echo '<li>No HOAs found</li>';
                }

                // Close the database connection
                $conn->close();
                ?>
            </ul>
        </div>
        <div class="record-details">
            <!-- Record details will be displayed here when an HOA is selected -->
        </div>
    </div>
</body>
</html>
