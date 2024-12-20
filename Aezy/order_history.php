<?php
// Include database connection
include('db.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the user
$query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all orders
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link rel="stylesheet" href="home.css">
    <script defer>
        // Toggle visibility of order items
        function toggleOrderDetails(orderId) {
            const itemsContainer = document.getElementById('order-items-' + orderId);
            if (itemsContainer.style.display === 'none') {
                itemsContainer.style.display = 'block';
            } else {
                itemsContainer.style.display = 'none';
            }
        }

        // Confirm order cancellation
        function confirmCancel(orderId) {
            const confirmation = confirm("Are you sure you want to cancel this order?");
            if (confirmation) {
                cancelOrder(orderId);
            }
        }

        // Cancel the order via AJAX
        function cancelOrder(orderId) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'cancel_order.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                    window.location.reload(); // Reload the page to reflect the status change
                } else {
                    alert('Error occurred. Please try again.');
                }
            };
            xhr.send('order_id=' + orderId);
        }
    </script>
</head>
<body>
    <header>
        <div class="logo">Aèzy Floral</div>
        <!-- Link to home.php -->
        <a href="home.php" class="home-link">Back to Home</a>
    </header>

    <h2>Your Order History</h2>

    <!-- Loop through orders -->
    <div class="order-history">
        <?php foreach ($orders as $order): ?>
            <div class="order">
                <div class="order-summary">
                    <h3>Order #<?php echo $order['order_id']; ?> - Status: <?php echo $order['status']; ?></h3>
                    <p>Total Amount: Php <?php echo $order['total_amount']; ?></p>
                    <p>Payment Mode: <?php echo ucfirst(str_replace('_', ' ', $order['payment_mode'])); ?></p>
                    <button onclick="toggleOrderDetails(<?php echo $order['order_id']; ?>)">View Items</button>
                    <?php if ($order['status'] === 'Pending'): ?>
                        <button class="cancel-btn" onclick="confirmCancel(<?php echo $order['order_id']; ?>)">Cancel Order</button>
                    <?php endif; ?>
                </div>

                <!-- Order Items (hidden by default) -->
                <div id="order-items-<?php echo $order['order_id']; ?>" class="order-items" style="display: none;">
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Product Id</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch order details based on order_id
                            $itemQuery = "SELECT product_id, quantity, price FROM order_details WHERE order_id = ?";
                            $itemStmt = $conn->prepare($itemQuery);
                            $itemStmt->bind_param('i', $order['order_id']); // Use order_id for fetching details
                            $itemStmt->execute();
                            $itemResult = $itemStmt->get_result();

                            // Display each order detail
                            while ($item = $itemResult->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?php echo $item['product_id']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>Php <?php echo number_format($item['price'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
