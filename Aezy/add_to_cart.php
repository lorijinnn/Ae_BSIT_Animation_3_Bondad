<?php
// Include database connection
include 'db.php';

$userId = $_POST['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Invalid user ID.");
}

// Continue with the cart insertion logic


// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve POST data
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $total_price = $_POST['total_price'];
    $user_id = $_POST['user_id'];

    // Check if the product is already in the cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If product exists in cart, update quantity, else insert new record
    if ($result->num_rows > 0) {
        // Product already in cart, update quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();
        echo "Product quantity updated in cart!";
    } else {
        // Insert new product to cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, product_name, quantity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $user_id, $product_id, $product_name, $quantity);
        $stmt->execute();
        echo "Product added to cart!";
    }

    $stmt->close();
    $conn->close();
}
?>
