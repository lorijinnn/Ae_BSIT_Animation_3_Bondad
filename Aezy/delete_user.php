<?php
include('db.php');
session_start();

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete the user from the database
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);

    if ($stmt->execute()) {
        echo "User removed successfully.";
    } else {
        echo "Error removing user.";
    }

    header("Location: manage_users.php");
}
?>
