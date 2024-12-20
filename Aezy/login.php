<!-- login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css"> <!-- Link to the CSS file -->
</head>
<body>
    <div class="login-page">
        <div class="login-form">
            <h2>Log In</h2>
            <form action="processlogin.php" method="POST">
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit">Log In</button>
                <button type="button" class="signup-btn" onclick="window.location.href='signup.php'">Sign Up</button>
            </form>
            <a href="home.php" class="exit-btn">Back to Home</a> <!-- Optionally add an exit link -->
        </div>
    </div>
</body>
</html>
