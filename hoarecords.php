<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOA Records</title>
    <link rel="stylesheet" href="recordstyle.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <?php
    session_start(); // Ensure session is started for message handling
    include 'dbconnection.php';

    // Display success or error messages
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
        unset($_SESSION['success_message']); // Clear the message after displaying it
    }

    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        unset($_SESSION['error_message']); // Clear the message after displaying it
    }

    // Get HOA name from the URL
    $hoa_name = isset($_GET['hoa']) ? urldecode($_GET['hoa']) : '';

    // Function to fetch members based on HOA
    function getMembers($conn, $hoa) {
        $stmt = $conn->prepare("SELECT id, name, member_id, lot_no, status, role FROM member WHERE hoa = ?");
        $stmt->bind_param("s", $hoa);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Fetch members for display
    $members_result = getMembers($conn, $hoa_name);
    ?>
</head>
<body>
    <div class="menu-bar">
        <h1>
            <?php
            echo $hoa_name ? htmlspecialchars($hoa_name) . " HOA Records" : "HOA Records";
            ?>
        </h1>
        <div class="menu-actions">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addRecordModal">Add Member</button>
            <a href="member_detail.php" class="btn btn-secondary">Scan QR Code</a>
            <a href="records.php" class="btn btn-light">Back</a>
        </div>
    </div>

    <div class="records-container">
    <div class="records-list">
        <ul id="membersList">
            <?php
            if ($members_result->num_rows > 0) {
                while ($row = $members_result->fetch_assoc()) {
                    // Make sure to enclose lot_no in single quotes for JavaScript string handling
                    echo '<li onclick="showDetails(\'' . htmlspecialchars($row["lot_no"]) . '\')">';
                    echo '<strong>' . htmlspecialchars($row["name"]) . '</strong><br>';
                    echo 'ID: ' . htmlspecialchars($row["member_id"]) . '<br>';
                    echo 'Lot Number: ' . htmlspecialchars($row["lot_no"]) . '<br>'; // Display Lot Number
                    echo 'Role: ' . htmlspecialchars($row["role"]) . '<br>';
                    echo 'Status: ' . htmlspecialchars($row["status"]) . '<br>';
                    echo '<form action="delete_record.php" method="POST" style="display:inline-block;" onsubmit="return confirmDelete(event);">';
                    echo '<input type="hidden" name="member_id" value="' . htmlspecialchars($row["id"]) . '">';
                    echo '<input type="hidden" name="hoa" value="' . htmlspecialchars($hoa_name) . '">';
                    echo '<button type="submit" class="btn btn-danger">Delete</button>';
                    echo '</form>';
                    echo '</li>';
                }
            } else {
                echo "<li>No members found</li>";
            }
            ?>
        </ul>
    </div>
    <div id="recordDetails" class="record-details"></div>
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
                            <select class="form-control" id="hoa" name="hoa" onchange="fetchAvailableLots()" required>
                                <option value="">Select HOA</option>
                                <?php
                                $stmt = $conn->prepare("SELECT DISTINCT hoa FROM polygons");
                                $stmt->execute();
                                $hoa_result = $stmt->get_result();

                                while ($hoa_row = $hoa_result->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($hoa_row['hoa']) . '">' . htmlspecialchars($hoa_row['hoa']) . '</option>';
                                }
                                $stmt->close();
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
                                <!-- Options will be populated dynamically by JavaScript -->
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Add Member</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- JavaScript for showing details and adding members -->
    <script>
       function showDetails(lotNo) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch_member_details.php?lot_no=' + encodeURIComponent(lotNo), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            var detailsHTML = response.error ? response.error :
                `<h3>Details for Lot Number: ${response.lot_no || 'N/A'}</h3>
                <p><strong>Name:</strong> ${response.name}</p>
                <p><strong>Member ID:</strong> ${response.member_id}</p>
                <p><strong>Address:</strong> ${response.address}</p>
                <p><strong>Phone Number:</strong> ${response.phone_number}</p>
                <p><strong>Email:</strong> ${response.email}</p>
                <p><strong>Membership Date:</strong> ${response.membership_date}</p>
                <p><strong>Status:</strong> ${response.status}</p>
                <p><strong>Role:</strong> ${response.role}</p>
                <p><strong>Notes:</strong> ${response.notes}</p>
                <img src="data:image/png;base64,${response.qr_code}" alt="QR Code" style="width: 150px; height: 150px;"/>`;
                
            document.getElementById('recordDetails').innerHTML = detailsHTML;
        }
    };
    xhr.send();
}


function fetchAvailableLots() {
    var hoa = document.getElementById('hoa').value;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_lot_numbers.php?hoa=' + encodeURIComponent(hoa), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                console.log(response); // Log the response to see its structure
                var lotSelect = document.getElementById('lot_no');
                lotSelect.innerHTML = '<option value="">Select Lot Number</option>'; // Clear previous options

                // Check if the response indicates success
                if (response.success) {
                    // Check if lots array is present and is an array
                    if (Array.isArray(response.lots)) {
                        response.lots.forEach(function(lot) {
                            var option = document.createElement('option');
                            option.value = lot.lot_no; // Accessing the lot_no directly
                            option.textContent = lot.lot_no; // Accessing the lot_no directly
                            lotSelect.appendChild(option);
                        });
                    } else {
                        console.error("Lots are not an array", response.lots);
                        alert("No lots available.");
                    }
                } else {
                    console.error("Error: " + response.message);
                    alert("Error fetching lots: " + response.message);
                }
            } catch (e) {
                console.error("Failed to parse JSON", e);
            }
        } else {
            console.error("Request failed with status", xhr.status);
        }
    };
    xhr.send();
}




        function addMember(event) {
            event.preventDefault(); // Prevent form submission

            var formData = new FormData(document.getElementById('addMemberForm'));
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_record.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Handle response
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert('Member added successfully!');
                        location.reload(); // Reload the page to see the new member
                    } else {
                        alert('Error: ' + response.error);
                    }
                }
            };
            xhr.send(formData);
        }

        function confirmDelete(event) {
            return confirm('Are you sure you want to delete this member?');
        }
    </script>
</body>
</html>
