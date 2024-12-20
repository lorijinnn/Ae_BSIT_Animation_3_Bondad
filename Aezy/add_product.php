<?php
include('db.php');
include('header.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_desc = $_POST['product_desc'];

    // Insert into database
    $query = "INSERT INTO products (product_name, product_price, product_desc) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $product_name, $product_price, $product_desc);
    $stmt->execute();

    // Redirect to manage products page
    header("Location: manage_products.php");
    exit();
}
?>

<h2>Add New Product</h2>
<form action="add_product.php" method="POST">
    <label for="product_name">Product Name:</label>
    <input type="text" name="product_name" required><br>

    <label for="product_price">Price:</label>
    <input type="text" name="product_price" required><br>

    <label for="product_desc">Description:</label>
    <textarea name="product_desc" required></textarea><br>

    <button type="submit">Add Product</button>
</form>
