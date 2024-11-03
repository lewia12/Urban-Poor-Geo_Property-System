<?php
session_start();
include 'dbconnection.php';

// Handle logout request
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employeeId = $_POST['employeeId'];
    $password = $_POST['password'];

    // Fetch user data, including role
    $sql = "SELECT id, password, role FROM users WHERE employeeid = '$employeeId'"; // Adjust your SQL accordingly
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $hashed_password = $user['password'];

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $userId = $user['id'];
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $userId; // Store user ID in session
            $_SESSION['role'] = $user['role']; // Store user role in session

            // Update last_login_time
            $updateSql = "UPDATE users SET last_login_time = NOW() WHERE id = '$userId'";
            $conn->query($updateSql); // Execute the query to update the timestamp
            
            header('Location: index.php');
            exit();
        } else {
            $login_error = "Invalid Employee ID or password";
        }
    } else {
        $login_error = "Invalid Employee ID or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="loginstyle.css"> <!-- Your custom CSS if needed -->
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="login-container bg-white p-4 rounded shadow">
            <h2 class="text-center">Login</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="employeeId">Employee ID:</label>
                    <input type="text" name="employeeId" id="employeeId" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <?php if (isset($login_error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($login_error) ?></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies (optional for this case) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
