<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blacklist Management</title>
    <link rel="stylesheet" href="recordstyle.css">
</head>
<body>
    <div class="menu-bar">
        <div class="menu-icon"></div>
        <h1>Blacklist Management</h1>
        <div>
            <button class="menu-button" onclick="window.location.href='index.php'">Home</button>
        </div>
    </div>

    <div class="records-container">
        <div class="records-list">
            <h2>Members</h2>
            <ul>
                <?php
                include 'dbconnection.php';

                // SQL query to fetch members not already in the blacklist
                $sql = "SELECT m.id, m.name, m.member_id, h.name as hoa_name 
                        FROM members m 
                        JOIN hoas h ON m.hoa_id = h.id 
                        WHERE m.id NOT IN (SELECT member_id FROM blacklist)";
                $result = $conn->query($sql);

                // Check if there are results
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<li>';
                        echo '<span>' . htmlspecialchars($row["name"]) . ' (ID: ' . htmlspecialchars($row["member_id"]) . ') - HOA: ' . htmlspecialchars($row["hoa_name"]) . '</span>';
                        echo '<form action="add_to_blacklist.php" method="POST" style="display:inline-block;">';
                        echo '<input type="hidden" name="member_id" value="' . htmlspecialchars($row["id"]) . '">';
                        echo '<input type="hidden" name="hoa_id" value="' . htmlspecialchars($row["hoa_name"]) . '">';
                        echo '<input type="hidden" name="name" value="' . htmlspecialchars($row["name"]) . '">';
                        echo '<button type="submit" class="menu-button">Blacklist</button>';
                        echo '</form>';
                        echo '</li>';
                    }
                } else {
                    echo "<li>No members found</li>";
                }

                // Close connection
                $conn->close();
                ?>
            </ul>
        </div>
    </div>
</body>
</html>
