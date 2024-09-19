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
        <div class="menu-icon"></div>
        <h1>
            <?php
            include 'dbconnection.php';

            // Function to get the HOA name
            function getHoaName($conn, $hoa_id) {
                $stmt = $conn->prepare("SELECT name FROM hoas WHERE id = ?");
                $stmt->bind_param("i", $hoa_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                return $row['name'];
            }

            $hoa_id = intval($_GET['id']);
            echo getHoaName($conn, $hoa_id) . " HOA Records";
            ?>
        </h1>

        <div class="menu-actions">
            <!-- Add Record Button -->
            <button class="menu-button" onclick="document.getElementById('addRecordModal').style.display='block'">Add Records</button>
            <button class="menu-button">Scan QR Code</button>
            <a href="records.php" class="menu-button">Back</a>
        </div>
    </div>

    <div class="records-container">
        <div class="records-list">
            <ul>
                <?php
                // Function to display HOA members
                function getMembers($conn, $hoa_id) {
                    $stmt = $conn->prepare("SELECT id, name, member_id, address, phone_number, email, membership_date, status, role, notes FROM members WHERE hoa_id = ?");
                    $stmt->bind_param("i", $hoa_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<li onclick="showDetails(' . htmlspecialchars($row["id"]) . ')">';
                            echo '<strong>' . htmlspecialchars($row["name"]) . '</strong><br>';
                            echo 'ID: ' . htmlspecialchars($row["member_id"]) . '<br>';
                            echo 'Role: ' . htmlspecialchars($row["role"]) . '<br>';
                            echo 'Status: ' . htmlspecialchars($row["status"]) . '<br>';
                            echo '<form action="delete_record.php" method="POST" style="display:inline-block;">';
                            echo '<input type="hidden" name="member_id" value="' . htmlspecialchars($row["id"]) . '">';
                            echo '<input type="hidden" name="hoa_id" value="' . htmlspecialchars($hoa_id) . '">';
                            echo '<button type="submit" class="menu-button" onclick="return confirm(\'Are you sure you want to delete this record?\')">Delete</button>';
                            echo '</form>';
                            echo '</li>';
                        }
                    } else {
                        echo "<li>No members found</li>";
                    }
                }

                // Call the function to display members
                getMembers($conn, $hoa_id);
                $conn->close();
                ?>
            </ul>
        </div>
        <div id="recordDetails" class="record-details">
            <!-- Record details will be shown here -->
        </div>
    </div>

    <!-- Add Record Modal -->
    <div id="addRecordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('addRecordModal').style.display='none'">&times;</span>
            <h2>Add New Member</h2>
            <form action="add_record.php" method="POST">
                <label for="name">Member Name:</label>
                <input type="text" id="name" name="name" required><br><br>

                <label for="member_id">Member ID:</label>
                <input type="text" id="member_id" name="member_id" required><br><br>

                <!-- Hidden input for the HOA ID -->
                <input type="hidden" name="hoa_id" value="<?php echo $hoa_id; ?>">

                <button type="submit">Add Member</button>
            </form>
        </div>
    </div>

    <!-- Modal Styling -->
    <style>
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 15% auto; padding: 20px; width: 300px; }
        .close { float: right; font-size: 20px; cursor: pointer; }
        .record-details { margin-left: 20px; }
    </style>

    <!-- JavaScript for showing record details -->
    <script>
    function showDetails(memberId) {
        // Create an XMLHttpRequest object
        var xhr = new XMLHttpRequest();

        // Configure it: GET-request for the URL /fetch_member_details.php?id=memberId
        xhr.open('GET', 'fetch_member_details.php?id=' + memberId, true);

        // Set up a function to be called when the request is completed
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                // Parse JSON response
                var response = JSON.parse(xhr.responseText);
                var detailsHTML = '';

                if (response.error) {
                    detailsHTML = response.error;
                } else {
                    // Create HTML string for member details
                    detailsHTML += '<h3>Details for Member ID: ' + response.member_id + '</h3>';
                    detailsHTML += '<p><strong>Name:</strong> ' + response.name + '</p>';
                    detailsHTML += '<p><strong>Address:</strong> ' + response.address + '</p>';
                    detailsHTML += '<p><strong>Phone Number:</strong> ' + response.phone_number + '</p>';
                    detailsHTML += '<p><strong>Email:</strong> ' + response.email + '</p>';
                    detailsHTML += '<p><strong>Membership Date:</strong> ' + response.membership_date + '</p>';
                    detailsHTML += '<p><strong>Status:</strong> ' + response.status + '</p>';
                    detailsHTML += '<p><strong>Role:</strong> ' + response.role + '</p>';
                    detailsHTML += '<p><strong>Notes:</strong> ' + response.notes + '</p>';
                }

                // Update the recordDetails section
                document.getElementById('recordDetails').innerHTML = detailsHTML;
            } else {
                // Handle error
                document.getElementById('recordDetails').innerHTML = 'Error fetching details';
            }
        };

        // Set up a function to be called if the request fails
        xhr.onerror = function() {
            document.getElementById('recordDetails').innerHTML = 'Request failed';
        };

        // Send the request
        xhr.send();
    }
</script>

</body>
</html>
