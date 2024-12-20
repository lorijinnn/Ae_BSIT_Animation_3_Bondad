<?php
// Include database connection
include('db.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to be logged in to cancel an order.";
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the order ID is provided
if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Fetch the order to check its status
    $query = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $order_id, $user_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if ($order) {
        // Check if the order is not shipped (status 'Completed' or 'Cancelled')
        if ($order['status'] == 'Pending') {
            // Update the order status to 'Cancelled'
            $updateQuery = "UPDATE orders SET status = 'Cancelled' WHERE order_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('i', $order_id);
            if ($updateStmt->execute()) {
                echo "Your order has been successfully cancelled.";
            } else {
                echo "Error: Could not cancel your order.";
            }
        } else {
            echo "This order cannot be cancelled because it is either completed or already cancelled.";
        }
    } else {
        echo "Order not found or you don't have permission to cancel this order.";
    }
} else {
    echo "No order ID provided.";
}
?>
