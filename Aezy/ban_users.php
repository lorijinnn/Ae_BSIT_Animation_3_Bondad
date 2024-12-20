<?php
include('db.php');
session_start();

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Update the user's status to banned
    $query = "UPDATE users SET is_banned = 1 WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);

    if ($stmt->execute()) {
        echo "User banned successfully.";
    } else {
        echo "Error banning user.";
    }

    header("Location: manage_users.php");
}
?>
