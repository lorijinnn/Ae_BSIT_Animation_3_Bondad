<?php
include('db.php'); // Database connection

// Fetch total sales (sum of all completed orders)
$totalSalesQuery = "SELECT SUM(total_amount) AS total_sales FROM orders WHERE status = 'Completed'";
$resultTotalSales = $conn->query($totalSalesQuery);
$totalSalesData = $resultTotalSales->fetch_assoc();
$totalSales = $totalSalesData['total_sales'];

// Fetch orders placed this month
$ordersThisMonthQuery = "SELECT COUNT(order_id) AS orders_this_month FROM orders WHERE MONTH(created_at) = MONTH(CURRENT_DATE())";
$resultOrdersThisMonth = $conn->query($ordersThisMonthQuery);
$ordersThisMonthData = $resultOrdersThisMonth->fetch_assoc();
$ordersThisMonth = $ordersThisMonthData['orders_this_month'];

// Fetch active users (users who have placed at least one order)
$activeUsersQuery = "SELECT COUNT(DISTINCT user_id) AS active_users FROM orders";
$resultActiveUsers = $conn->query($activeUsersQuery);
$activeUsersData = $resultActiveUsers->fetch_assoc();
$activeUsers = $activeUsersData['active_users'];

// Fetch pending orders
$pendingOrdersQuery = "SELECT COUNT(order_id) AS pending_orders FROM orders WHERE status = 'Pending'";
$resultPendingOrders = $conn->query($pendingOrdersQuery);
$pendingOrdersData = $resultPendingOrders->fetch_assoc();
$pendingOrders = $pendingOrdersData['pending_orders'];

// Fetch top-selling items
$topSellingQuery = "SELECT product_name, SUM(quantity) AS total_sales
                    FROM order_details
                    JOIN products ON order_details.product_id = products.product_id
                    GROUP BY product_name
                    ORDER BY total_sales DESC LIMIT 5";
$resultTopSelling = $conn->query($topSellingQuery);
$topSelling = $resultTopSelling->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Aèzy Floral</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <header>
    Admin Dashboard - Aèzy Floral
  </header>

  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>Navigation</h2>
      <a href="admin_homepage.php">Dashboard</a>
      <a href="manage_users.php">Manage Users</a>
      <a href="manage_products.php">Manage Products</a>
      <a href="manage_orders.php">Orders</a>
      <a href="reports.php">Reports</a>
      <a href="settings.php">Settings</a>
      <a href="logout.php">Logout</a>
    </aside>

    <!-- Main Content -->
    <div class="content">
      <h1>Welcome, Admin!</h1>
      <p>Here is an overview of your shop's performance:</p>

      <!-- Statistics Section -->
      <div class="dashboard-grid">
        <div class="dashboard-card stats">
          <h3>Total Sales</h3>
          <h2>Php <?php echo number_format($totalSales, 2); ?></h2>
        </div>

        <div class="dashboard-card stats">
          <h3>Orders This Month</h3>
          <h2><?php echo $ordersThisMonth; ?></h2>
        </div>

        <div class="dashboard-card stats">
          <h3>Active Users</h3>
          <h2><?php echo $activeUsers; ?></h2>
        </div>

        <div class="dashboard-card stats">
          <h3>Pending Orders</h3>
          <h2><?php echo $pendingOrders; ?></h2>
        </div>
      </div>

      <!-- Quick Actions Section -->
      <h2>Quick Actions</h2>
      <div class="dashboard-grid">
        <div class="dashboard-card">
          <h3>Top Selling Items</h3>
          <ul>
            <?php foreach ($topSelling as $item): ?>
              <li><?php echo $item['product_name']; ?> (<?php echo $item['total_sales']; ?> sold)</li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="dashboard-card">
          <h3>Manage Products</h3>
          <p>Add, edit, or remove products from your store.</p>
          <a href="manage_products.php" class="btn">Go to Products</a>
        </div>

        <div class="dashboard-card">
          <h3>View Orders</h3>
          <p>Review and update customer orders.</p>
          <a href="manage_orders.php" class="btn">Go to Orders</a>
        </div>

        <div class="dashboard-card">
          <h3>User Management</h3>
          <p>View and manage registered users.</p>
          <a href="manage_users.php" class="btn">Go to Users</a>
        </div>

        <div class="dashboard-card">
          <h3>Reports</h3>
          <p>Generate sales and performance reports.</p>
          <a href="reports.php" class="btn">Go to Reports</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
