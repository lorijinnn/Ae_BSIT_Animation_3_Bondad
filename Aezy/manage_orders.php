<?php
include('db.php');
include('header.php');
session_start();

// Fetch all orders
$query = "SELECT * FROM orders";
$result = $conn->query($query);
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>
<h2>Manage Orders</h2>
<table>
  <thead>
    <tr>
      <th>Order ID</th>
      <th>User ID</th>
      <th>Total Amount</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($orders as $order): ?>
      <tr>
        <td><?php echo $order['order_id']; ?></td>
        <td><?php echo $order['user_id']; ?></td>
        <td>Php <?php echo number_format($order['total_amount'], 2); ?></td>
        <td><?php echo $order['status']; ?></td>
        <td>
          <form method="POST" action="update_order_status.php">
            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
            <select name="status">
              <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
              <option value="Completed" <?php echo $order['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
              <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <button type="submit">Update</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
