<?php
include('db.php');
include('header.php');
session_start();

// Fetch all products
$query = "SELECT * FROM products";
$result = $conn->query($query);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>
<h2>Manage Products</h2>
<a href="add_product.php">Add New Product</a>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Price</th>
      <th>Description</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($products as $product): ?>
      <tr>
        <td><?php echo $product['product_id']; ?></td>
        <td><?php echo $product['product_name']; ?></td>
        <td>Php <?php echo number_format($product['product_price'], 2); ?></td>
        <td><?php echo $product['product_desc']; ?></td>
        <td>
          <a href="edit_product.php?id=<?php echo $product['product_id']; ?>">Edit</a>
          <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
