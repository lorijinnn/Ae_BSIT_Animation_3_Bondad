<?php
// Include database connection
include 'db.php';

if (!isset($_POST['user_id'], $_POST['product_id'], $_POST['quantity_change'])) {
    die('Invalid request');
}

$userId = $_POST['user_id'];
$productId = $_POST['product_id'];
$quantityChange = $_POST['quantity_change'];

// Check if the user has the product in the cart
$query = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $userId, $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Product exists, update quantity
    $query = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iii', $quantityChange, $userId, $productId);
    $stmt->execute();
    echo "Quantity updated!";
} else {
    echo "Product not found in cart!";
}

$stmt->close();
$conn->close();
?>
