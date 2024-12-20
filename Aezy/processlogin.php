<?php
include 'db.php';  // Include your database connection file

session_start();

$username = $password = "";
$usernameErr = $passwordErr = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Clean input data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate input fields
    if (empty($username)) {
        $usernameErr = "Username is required.";
    }

    if (empty($password)) {
        $passwordErr = "Password is required.";
    }

    // If no errors, check login credentials
    if (empty($usernameErr) && empty($passwordErr)) {
        // Query to check if the user exists in the database
        $stmt = $conn->prepare("SELECT user_id, fullname, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $fullname, $db_username, $db_password, $role);

        if ($stmt->fetch() && $password === $db_password) {
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;  // Store role in session

            // Redirect based on role
            if ($role === 'admin') {
                header("Location: admin_homepage.php");
                exit();
            } else {
                header("Location: home.php");
                exit();
            }
        } else {
            // Invalid credentials
            $loginErr = "Invalid username or password.";
            // Redirect back to login page with error
            header("Location: login.php?loginErr=" . urlencode($loginErr));
            exit();
        }

        $stmt->close();
    } else {
        // If fields are empty, redirect back to the login page with error messages
        header("Location: login.php?usernameErr=" . urlencode($usernameErr) . "&passwordErr=" . urlencode($passwordErr));
        exit();
    }
}

$conn->close();
