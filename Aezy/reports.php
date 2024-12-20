<?php
include('db.php');
include('header.php');
session_start();


// Top Selling Items
$sellingQuery = "SELECT product_name, SUM(quantity) AS total_sales
                 FROM order_details
                 JOIN products ON order_details.product_id = products.product_id
                 GROUP BY product_name
                 ORDER BY total_sales DESC LIMIT 2";
$sellingResult = $conn->query($sellingQuery);
$topSelling = $sellingResult->fetch_all(MYSQLI_ASSOC);

// Top Buying Users
$buyingQuery = "SELECT fullname, COUNT(orders.order_id) AS total_orders
                FROM orders
                JOIN users ON orders.user_id = users.user_id
                GROUP BY users.user_id
                ORDER BY total_orders DESC LIMIT 2";
$buyingResult = $conn->query($buyingQuery);
$topBuyingUsers = $buyingResult->fetch_all(MYSQLI_ASSOC);

// Monthly Sales
$monthlySalesQuery = "SELECT SUM(total_amount) AS monthly_sales
                      FROM orders
                      WHERE MONTH(created_at) = MONTH(CURRENT_DATE())";
$monthlySalesResult = $conn->query($monthlySalesQuery);
$monthlySales = $monthlySalesResult->fetch_assoc();
?>

<h2>Reports</h2>

<h3>Top Selling Items</h3>
<table>
  <thead>
    <tr>
      <th>Product Name</th>
      <th>Total Sales</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($topSelling as $item): ?>
      <tr>
        <td><?php echo $item['product_name']; ?></td>
        <td><?php echo $item['total_sales']; ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h3>Top Buying Users</h3>
<table>
  <thead>
    <tr>
      <th>User Name</th>
      <th>Total Orders</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($topBuyingUsers as $user): ?>
      <tr>
        <td><?php echo $user['fullname']; ?></td>
        <td><?php echo $user['total_orders']; ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h3>Monthly Sales</h3>
<p>Php <?php echo number_format($monthlySales['monthly_sales'], 2); ?></p>
