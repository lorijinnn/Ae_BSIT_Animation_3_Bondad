<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$itemDetails = [];

$query = "SELECT products.product_id, products.product_name, products.product_price, cart.quantity 
          FROM cart 
          JOIN products ON cart.product_id = products.product_id 
          WHERE cart.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $itemDetails[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'];
    $paymentMode = $_POST['payment_mode'];

    if (empty($itemDetails)) {
        echo "<p>Error: No items in your cart.</p>";
        exit();
    }

    $conn->begin_transaction();

    try {
        $totalAmount = array_reduce($itemDetails, function ($sum, $item) {
            return $sum + ($item['product_price'] * $item['quantity']);
        }, 0);

        $orderQuery = "INSERT INTO orders (user_id, total_amount, status, payment_mode, address, created_at) 
                       VALUES (?, ?, 'Pending', ?, ?, NOW())";
        $stmt = $conn->prepare($orderQuery);
        $stmt->bind_param('idss', $userId, $totalAmount, $paymentMode, $address);
        $stmt->execute();
        $orderId = $stmt->insert_id;

        $detailQuery = "INSERT INTO order_details (order_id, product_id, quantity, price, total) 
                        VALUES (?, ?, ?, ?, ?)";
        $detailStmt = $conn->prepare($detailQuery);

        foreach ($itemDetails as $item) {
            $itemTotal = $item['product_price'] * $item['quantity'];
            $detailStmt->bind_param('iiidi', $orderId, $item['product_id'], $item['quantity'], $item['product_price'], $itemTotal);
            $detailStmt->execute();
        }

        $deleteQuery = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param('i', $userId);
        $stmt->execute();

        $conn->commit();
        header("Location: order_history.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Checkout error: " . $e->getMessage());
        echo "<p>Something went wrong. Please try again later.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>
<body>
    <h1>Checkout</h1>
    <?php if (!empty($itemDetails)): ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $totalAmount = 0; ?>
                <?php foreach ($itemDetails as $item): ?>
                    <?php $itemTotal = $item['product_price'] * $item['quantity']; ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= number_format($item['product_price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($itemTotal, 2) ?></td>
                    </tr>
                    <?php $totalAmount += $itemTotal; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p>Total: <?= number_format($totalAmount, 2) ?></p>
        <form method="POST">
            <label for="address">Shipping Address:</label>
            <textarea id="address" name="address" required></textarea><br>

            <label for="payment_mode">Payment Mode:</label>
            <select id="payment_mode" name="payment_mode" required>
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
            </select><br>
            <button type="submit">Place Order</button>
        </form>
    <?php else: ?>
        <p>No items in your cart.</p>
    <?php endif; ?>
</body>
</html>
