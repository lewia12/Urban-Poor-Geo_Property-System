<?php
session_start(); // Start the session
include 'dbconnection.php'; // Assuming this file contains your DB connection

// Check if the user is logged in and has admin role
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php'); // Redirect to login page
    exit();
}

$message = ""; // Message to show success or error feedback

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $employeeid = $_POST['employeeid'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $dob = $_POST['dob'];
    $role = $_POST['role'];
    $new_password = $_POST['password'];

    // Hash the password before storing it
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Use prepared statements for security
    $stmt = $conn->prepare("INSERT INTO users (employeeid, firstname, lastname, dob, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssss", $employeeid, $firstname, $lastname, $dob, $hashed_password, $role);
    
    if ($stmt->execute()) {
        $message = "New user created successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];

    // Use prepared statements for security
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $message = "User deleted successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all users and their last login time
$sql = "SELECT id, employeeid, firstname, lastname, dob, last_login_time, created_at, role FROM users";
$result = $conn->query($sql);

// Fetch login history sorted by most recent login
$loginHistorySql = "SELECT employeeid, last_login_time FROM users ORDER BY last_login_time DESC";
$loginHistoryResult = $conn->query($loginHistorySql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Users</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</head>
<body>
   <!-- Top Navbar -->
   <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <div class="container mt-5">
        <h1>User Management</h1>

        <!-- Feedback Message -->
        <?php if ($message): ?>
            <div class="alert alert-info" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Button to trigger the Create User modal -->
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createUserModal">
            Create New User
        </button>

        <!-- Modal for creating new user -->
        <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="employeeid">Employee ID</label>
                                <input type="text" class="form-control" name="employeeid" id="employeeid" required pattern="\d+" title="Employee ID should only contain numbers">
                            </div>
                            <div class="form-group">
                                <label for="firstname">First Name</label>
                                <input type="text" class="form-control" name="firstname" id="firstname" required>
                            </div>
                            <div class="form-group">
                                <label for="lastname">Last Name</label>
                                <input type="text" class="form-control" name="lastname" id="lastname" required>
                            </div>
                            <div class="form-group">
                                <label for="dob">Date of Birth</label>
                                <input type="date" class="form-control" name="dob" id="dob" required>
                            </div>
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select class="form-control" name="role" id="role" required>
                                    <option value="employee">Employee</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" id="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="create_user">Create User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <h2>Existing Users</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Date of Birth</th>
                    <th>Role</th>
                    <th>Last Login Time</th>
                    <th>Account Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['employeeid']) ?></td>
                            <td><?= htmlspecialchars($row['firstname']) ?></td>
                            <td><?= htmlspecialchars($row['lastname']) ?></td>
                            <td><?= htmlspecialchars($row['dob']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td><?= htmlspecialchars($row['last_login_time']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td>
                                <a href="?delete_user=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Login History -->
        <h2>Login History</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Last Login Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($loginHistoryResult->num_rows > 0): ?>
                    <?php while($history = $loginHistoryResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($history['employeeid']) ?></td>
                            <td><?= htmlspecialchars($history['last_login_time']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No login history found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
