<?php
// Include database connection
include 'db.php';

if (!isset($_POST['user_id'], $_POST['product_id'])) {
    die('Invalid request');
}

$userId = $_POST['user_id'];
$productId = $_POST['product_id'];

// Remove item from cart
$query = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $userId, $productId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Item removed from cart!";
} else {
    echo "Failed to remove item.";
}

$stmt->close();
$conn->close();
?>
