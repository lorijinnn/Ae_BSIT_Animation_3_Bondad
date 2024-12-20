<?php
include('db.php');
include('header.php');
session_start();

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product details
    $query = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_desc = $_POST['product_desc'];

    // Update product in the database
    $query = "UPDATE products SET product_name = ?, product_price = ?, product_desc = ? WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssi', $product_name, $product_price, $product_desc, $product_id);
    $stmt->execute();

    // Redirect to manage products page
    header("Location: manage_products.php");
    exit();
}
?>

<h2>Edit Product</h2>
<form action="edit_product.php?id=<?php echo $product['product_id']; ?>" method="POST">
    <label for="product_name">Product Name:</label>
    <input type="text" name="product_name" value="<?php echo $product['product_name']; ?>" required><br>

    <label for="product_price">Price:</label>
    <input type="text" name="product_price" value="<?php echo $product['product_price']; ?>" required><br>

    <label for="product_desc">Description:</label>
    <textarea name="product_desc" required><?php echo $product['product_desc']; ?></textarea><br>

    <button type="submit">Update Product</button>
</form>
