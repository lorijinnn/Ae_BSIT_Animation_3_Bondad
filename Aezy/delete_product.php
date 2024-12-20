<?php
include('db.php');
include('header.php');
session_start();

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Delete product from the database
    $query = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();

    // Redirect to manage products page
    header("Location: manage_products.php");
    exit();
} else {
    // If no product ID is passed
    echo "Product ID not provided.";
}
