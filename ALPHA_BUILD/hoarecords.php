<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOA Records</title>
    <link rel="stylesheet" href="recordstyle.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="menu-bar">
        <div class="menu-icon"></div>
        <h1>
            <?php
            include 'dbconnection.php';

            // Get the HOA from the query parameter
            $hoa_name = isset($_GET['hoa']) ? urldecode($_GET['hoa']) : '';

            if ($hoa_name) {
                echo htmlspecialchars($hoa_name) . " HOA Records";
            } else {
                echo "HOA Records";
            }
            ?>
        </h1>

        <div class="menu-actions">
            <!-- Add Record Button -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#addRecordModal">Add Member</button>
            <button class="btn btn-secondary">Scan QR Code</button>
            <a href="records.php" class="btn btn-light">Back</a>
        </div>
    </div>

    <div class="records-container">
        <div class="records-list">
            <ul>
                <?php
                // Function to display HOA members
                function getMembers($conn, $hoa) {
                    $stmt = $conn->prepare("SELECT id, name, member_id, address, phone_number, email, membership_date, status, role, notes FROM member WHERE hoa = ?");
                    $stmt->bind_param("s", $hoa); // Assuming 'hoa' is a string
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<li onclick="showDetails(' . htmlspecialchars($row["id"]) . ')">';
                            echo '<strong>' . htmlspecialchars($row["name"]) . '</strong><br>';
                            echo 'ID: ' . htmlspecialchars($row["member_id"]) . '<br>';
                            echo 'Role: ' . htmlspecialchars($row["role"]) . '<br>';
                            echo 'Status: ' . htmlspecialchars($row["status"]) . '<br>';
                            echo '<form action="delete_record.php" method="POST" style="display:inline-block;">';
                            echo '<input type="hidden" name="member_id" value="' . htmlspecialchars($row["id"]) . '">';
                            echo '<input type="hidden" name="hoa" value="' . htmlspecialchars($hoa) . '">';
                            echo '<button type="submit" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this record?\')">Delete</button>';
                            echo '</form>';
                            echo '</li>';
                        }
                    } else {
                        echo "<li>No members found</li>";
                    }
                }

                // Call the function to display members using the fetched HOA
                getMembers($conn, $hoa_name);
                $conn->close();
                ?>
            </ul>
        </div>
        <div id="recordDetails" class="record-details">
            <!-- Record details will be shown here -->
        </div>
    </div>

    <!-- Add Member Modal -->
    <div id="addRecordModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRecordModalLabel">Add New Member</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addMemberForm" onsubmit="return addMember(event);">
                    <div class="form-group">
    <label for="hoa">Select HOA:</label>
    <select class="form-control" id="hoa" name="hoa" required>
        <option value="">Select HOA</option>
        <?php
        // Fetch existing HOAs from the polygons table
        include 'dbconnection.php';
        $stmt = $conn->prepare("SELECT DISTINCT hoa FROM polygons");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($hoa_row = $result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($hoa_row['hoa']) . '">' . htmlspecialchars($hoa_row['hoa']) . '</option>';
        }

        $stmt->close();
        $conn->close();
        ?>
    </select>
</div>

                        <div class="form-group">
                            <label for="name">Member Name:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="member_id">Member ID:</label>
                            <input type="text" class="form-control" id="member_id" name="member_id" required>
                        </div>

                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>

                        <div class="form-group">
                            <label for="phone_number">Phone Number:</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="membership_date">Membership Date:</label>
                            <input type="date" class="form-control" id="membership_date" name="membership_date" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="role">Role:</label>
                            <input type="text" class="form-control" id="role" name="role" required>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes:</label>
                            <textarea class="form-control" id="notes" name="notes"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="lot_no">Assign Lot Number:</label>
                            <select class="form-control" id="lot_no" name="lot_no" required>
                                <option value="">Select Lot Number</option>
                                <?php
                                include 'dbconnection.php';

                                // Fetch existing lot numbers from the database that are not assigned to any member
                                $stmt = $conn->prepare("
                                    SELECT lot_no 
                                    FROM polygons 
                                    WHERE lot_no NOT IN (SELECT lot_no FROM member WHERE hoa = ?)
                                ");
                                $stmt->bind_param("s", $hoa_name); // Assuming $hoa_name is defined earlier in the script
                                $stmt->execute();
                                $result = $stmt->get_result();

                                while ($lot = $result->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($lot['lot_no']) . '">' . htmlspecialchars($lot['lot_no']) . '</option>';
                                }

                                $stmt->close();
                                $conn->close();
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Add Member</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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

                // Display the details in the recordDetails div
                document.getElementById('recordDetails').innerHTML = detailsHTML;
            } else {
                console.error('Failed to fetch details. Status: ' + xhr.status);
            }
        };

        // Send the request
        xhr.send();
    }

    function addMember(event) {
        event.preventDefault(); // Prevent the default form submission

        // Gather form data
        var formData = new FormData(document.getElementById('addMemberForm'));

        // Create an XMLHttpRequest object
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_record.php', true);

        // Handle the response
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                // Refresh the members list (you could also implement specific DOM updates)
                location.reload(); // Reload the page to reflect changes
            } else {
                console.error('Failed to add member. Status: ' + xhr.status);
            }
        };

        // Send the request with the form data
        xhr.send(formData);
    }
    </script>
</body>
</html>
