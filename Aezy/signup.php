<?php
include 'db.php';  // Include your database connection file

// Variables for form data and error messages
$fullname = $username = $password = $address = $contactno = $role = "";
$fullnameErr = $usernameErr = $passwordErr = $addressErr = $contactErr = "";
$valid = true;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $contactno = $_POST['contactno'];
    $role = $_POST['role'];  // New role field

    // Form validation
    if (empty($fullname)) {
        $fullnameErr = "Full Name is required.";
        $valid = false;
    }
    if (empty($username)) {
        $usernameErr = "Username is required.";
        $valid = false;
    }
    if (strlen($password) < 6) {
        $passwordErr = "Password must be at least 6 characters.";
        $valid = false;
    }
    if (empty($address)) {
        $addressErr = "Address is required.";
        $valid = false;
    }

    // If form is valid, insert data into the database
    if ($valid) {
        // No password hashing (plain text password)
        // Prepare and bind SQL statement
        $stmt = $conn->prepare("INSERT INTO users (fullname, username, password, address, contactno, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $fullname, $username, $password, $address, $contactno, $role);

        // Execute query
        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .signup-container {
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }
    .signup-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #944e5c;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      font-size: 14px;
      color: #333;
    }
    .form-group input {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 14px;
      margin-top: 5px;
    }
    .form-group input[type="submit"] {
      background-color: #944e5c;
      color: white;
      cursor: pointer;
      border: none;
    }
    .form-group input[type="exit"] {
      background-color: #944e5c;
      color: white;
      cursor: pointer;
      border: none;
    }
    .form-group input[type="submit"]:hover {
      background-color: #ad6273;
    }
    .error-message {
      color: red;
      font-size: 12px;
    }
  </style>
</head>
<body>

  <div class="signup-container">
    <h2>Create Account</h2>
    <form action="signup.php" method="POST">
      <div class="form-group">
        <label for="fullname">Full Name</label>
        <input type="text" id="fullname" name="fullname" value="<?php echo $fullname; ?>" required>
        <span class="error-message"><?php echo $fullnameErr; ?></span>
      </div>

      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
        <span class="error-message"><?php echo $usernameErr; ?></span>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <span class="error-message"><?php echo $passwordErr; ?></span>
      </div>

      <div class="form-group">
        <label for="address">Address</label>
        <input type="text" id="address" name="address" value="<?php echo $address; ?>" required>
        <span class="error-message"><?php echo $addressErr; ?></span>
      </div>

      <div class="form-group">
        <label for="contactno">Contact Number</label>
        <input type="tel" id="contactno" name="contactno" value="<?php echo $contactno; ?>" required>
        <span class="error-message"><?php echo $contactErr; ?></span>
      </div>

      <!-- New Role Dropdown -->
      <div class="form-group">
        <label for="role">Role</label>
        <select name="role" id="role" required>
          <option value="customer" <?php if ($role == 'customer') echo 'selected'; ?>>Customer</option>
          <option value="admin" <?php if ($role == 'admin') echo 'selected'; ?>>Admin</option>
        </select>
      </div>

      <div class="form-group">
        <input type="submit" value="Sign Up">
      </div>

      <div class="form-group">
        <p>Already have an account? <a href="login.php">Login here</a></p>
      </div>

      <div class="form-group">
        <a href="home.php"><input type="exit" value="EXIT"></a>
      </div>
    </form>
  </div>

</body>
</html>
