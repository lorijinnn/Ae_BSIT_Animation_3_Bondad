<?php
include 'db.php';
session_start();

if (!isset($_POST['user_id'])) {
    echo 'User not logged in.';
    exit();
}

$userId = $_POST['user_id'];

// Fetch cart items for the user
$query = "SELECT cart.product_id, products.product_name, products.product_price, cart.quantity 
          FROM cart 
          JOIN products ON cart.product_id = products.product_id 
          WHERE cart.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

// Initialize the output
$cartItems = '';
$totalAmount = 0;

while ($item = $result->fetch_assoc()) {
    $itemTotal = $item['product_price'] * $item['quantity'];
    $totalAmount += $itemTotal;
    
    $cartItems .= "
        <div class='cart-item' id='cart-item-{$item['product_id']}'>
            <span>{$item['product_name']}</span>
            <span>Php " . number_format($item['product_price'], 2) . "</span>
            <div class='quantity-control'>
                <button onclick='updateQuantity({$item['product_id']}, -1)'>-</button>
                <span>{$item['quantity']}</span>
                <button onclick='updateQuantity({$item['product_id']}, 1)'>+</button>
            </div>
            <span>Php " . number_format($itemTotal, 2) . "</span>
            <button onclick='removeItem({$item['product_id']})'>Remove</button>
        </div>
    ";
}

// Return the cart items and total amount
echo $cartItems;
echo "<p>Total: Php <span id='totalAmount'>" . number_format($totalAmount, 2) . "</span></p>";
?>
