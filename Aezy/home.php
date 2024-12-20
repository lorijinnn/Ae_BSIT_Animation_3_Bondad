<?php
// Include the database connection
include('db.php');
session_start(); // Start the session to check login status

// Query to fetch all products
$query = "SELECT * FROM products";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $products = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
} else {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aèzy Floral</title>
  <link rel="stylesheet" href="home.css">
  <link rel="stylesheet" href="proddesign.css">
</head>
<body>
  <header class="header">
    <div class="logo">Aèzy floral</div>
    <nav class="nav">
      <ul class="menu">
        <li><a href="#">Home</a></li>
        <li><a href="order_history.php">Orders</a></li>
        <li class="dropdown">
          <a href="#">Flowers</a>
          <ul class="submenu">
            <li><a href="#">Roses</a></li>
            <li><a href="#">Tulips</a></li>
            <li><a href="#">Orchids</a></li>
          </ul>
        </li>
        <li><a href="#">Occasions</a></li>
        <li><a href="#">Combo</a></li>
        <li><a href="#">Sympathy & Funeral</a></li>
        <li><a href="#">Arrangements</a></li>
      </ul>
    </nav>
    <div class="icons">
    <span class="icon" onclick="openCartModal()">🛒</span>
      <a href="signup.php"><span class="icon">👤</span></a> |
      <?php if (isset($_SESSION['user_id'])): ?> <!-- Check if the user is logged in -->
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a> <!-- If not logged in, show login link -->
      <?php endif; ?>
    </div>
  </header>

  <div class="search-bar">
    <div class="search-container">
      <input type="text" class="search-input" id="searchInput" placeholder="Search flowers, occasions..." />
      <button class="search-button" onclick="performSearch()">🔍</button>
    </div>
  </div>

  <main class="hero">
    <img src="images/board.jpg" alt="A beautiful floral arrangement" class="hero-image">
</main>


  <section class="about-shop">
    <h2>Featured Products</h2>
    <p>For the Month of December</p>
  </section>

  <!-- Product Showcase -->
  <section class="product-showcase">
    <?php foreach ($products as $product): ?>
      <div class="product-item">
        <img src="images/<?php echo $product['product_id']; ?>.jpeg" alt="<?php echo $product['product_name']; ?>" class="product-image">
        <h3 class="product-name"><?php echo $product['product_name']; ?></h3>
        <p class="product-price">Php <?php echo $product['product_price']; ?></p>
        <!-- The Add to Cart button -->
        <button class="btn view-btn" onclick="openAddToCartModal('<?php echo $product['product_id']; ?>', '<?php echo $product['product_name']; ?>', '<?php echo $product['product_price']; ?>', '<?php echo addslashes($product['product_desc']); ?>')">Add to Cart</button>
      </div>
    <?php endforeach; ?>
  </section>

  <!-- Modal for Product Details -->
  <div id="productModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <img id="modalImage" src="" alt="Product Image" class="modal-image">
      <h3 id="modalTitle">Product Name</h3>
      <p id="modalPrice">Price</p>
      <p id="modalDescription">Description</p>
      <label for="modalQuantity">Quantity: </label>
      <input type="number" id="modalQuantity" value="1" min="1" onchange="updateTotalPrice()">
      <p>Total Price: Php <span id="modalTotalPrice">0.00</span></p>
      <button id="modalAddToCartButton" onclick="addToCart()">Add to Cart</button>
    </div>
  </div>

  <!-- Script Block (Place this right before </body>) -->
  <script>
    function viewProduct(productId, productName, productPrice, productDesc) {
      // Set modal content
      document.getElementById('modalImage').src = 'img/' + productId + '.jpeg';
      document.getElementById('modalTitle').textContent = productName;
      document.getElementById('modalPrice').textContent = 'Php ' + productPrice;
      document.getElementById('modalDescription').textContent = productDesc;
      document.getElementById('modalTotalPrice').textContent = productPrice; // Initial total price
      document.getElementById('modalQuantity').value = 1; // Reset quantity
      document.getElementById('productModal').style.display = 'block';
    }

    function closeModal() {
      document.getElementById('productModal').style.display = 'none';
    }

    function updateTotalPrice() {
      const price = parseFloat(document.getElementById('modalPrice').textContent.replace('Php ', ''));
      const quantity = document.getElementById('modalQuantity').value;
      const totalPrice = price * quantity;
      document.getElementById('modalTotalPrice').textContent = totalPrice.toFixed(2);
    }

    function openAddToCartModal(productId, productName, productPrice, productDesc) {
      // Set data for the modal
      window.selectedProduct = { id: productId, name: productName, price: parseFloat(productPrice), description: productDesc };
      viewProduct(productId, productName, productPrice, productDesc);
      document.getElementById('productModal').style.display = 'block';
    }

    // Check if user is logged in only when they try to add to the cart
    function addToCart() {
      const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

      if (userId === null) {
        alert('Please log in first to add items to your cart.');
        window.location.href = 'login.php'; // Redirect to login page if not logged in
        return;
      }

      const productId = window.selectedProduct.id;
      const productName = window.selectedProduct.name;
      const productDesc = window.selectedProduct.description;
      const quantity = document.getElementById('modalQuantity').value;
      const totalPrice = document.getElementById('modalTotalPrice').textContent;

      // Send data to the server (AJAX request)
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "add_to_cart.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      xhr.onload = function() {
        if (xhr.status === 200) {
          alert(xhr.responseText);  // Show success or failure message
          closeModal();             // Close the modal
        } else {
          alert('An error occurred. Please try again.');
        }
      };

      xhr.send("product_id=" + productId + "&product_name=" + encodeURIComponent(productName) + "&product_desc=" + encodeURIComponent(productDesc) + "&quantity=" + quantity + "&total_price=" + totalPrice + "&user_id=" + userId);
    }
  </script>



<!-- Cart Modal (Hidden by default) -->
<div id="cartModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeCartModal()">&times;</span>
    <h3>Your Cart</h3>
    <div id="cartItemsContainer">
      <!-- Cart items will be dynamically loaded here -->
    </div>
    <div>
      <p>Total: Php <span id="totalAmount">0.00</span></p>
      <button id="checkoutButton" onclick="proceedToCheckout()">Proceed to Checkout</button>
    </div>
  </div>
</div>

<script>
  // Open Cart Modal
  function openCartModal() {
    const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
    if (userId === null) {
      alert('Please log in first to view your cart.');
      window.location.href = 'login.php'; // Redirect to login page if not logged in
      return;
    }

    // Fetch cart items from the server
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'fetch_cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      if (xhr.status === 200) {
        document.getElementById('cartItemsContainer').innerHTML = xhr.responseText;
        document.getElementById('cartModal').style.display = 'block';
      } else {
        alert('An error occurred while fetching your cart items.');
      }
    };
    xhr.send('user_id=' + userId);
  }

  // Close Cart Modal
  function closeCartModal() {
    document.getElementById('cartModal').style.display = 'none';
  }

  // Update the quantity of an item in the cart
  function updateQuantity(productId, quantityChange) {
    const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
    if (userId === null) {
      alert('Please log in first.');
      return;
    }

    // Send the quantity change to the server
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      if (xhr.status === 200) {
        // Recalculate the total and update the UI
        fetchCartItems();
      }
    };
    xhr.send(`user_id=${userId}&product_id=${productId}&quantity_change=${quantityChange}`);
  }

  // Remove item from cart
  function removeItem(productId) {
    const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
    if (userId === null) {
      alert('Please log in first.');
      return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'remove_from_cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      if (xhr.status === 200) {
        fetchCartItems();
      }
    };
    xhr.send(`user_id=${userId}&product_id=${productId}`);
  }

  // Fetch cart items from the server and update total
  function fetchCartItems() {
    const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
    if (userId === null) {
      alert('Please log in first.');
      return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'fetch_cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      if (xhr.status === 200) {
        document.getElementById('cartItemsContainer').innerHTML = xhr.responseText;
        updateTotalAmount();
      }
    };
    xhr.send('user_id=' + userId);
  }

  // Update the total amount of selected items
  function updateTotalAmount() {
    const totalAmountElement = document.getElementById('totalAmount');
    let total = 0;
    const prices = document.querySelectorAll('.cart-item');
    prices.forEach(item => {
      const itemTotal = parseFloat(item.querySelector('.total-price').innerText.replace('Php ', '').trim());
      total += itemTotal;
    });
    totalAmountElement.innerText = total.toFixed(2);
  }

  // Proceed to checkout
  function proceedToCheckout() {
    const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
    if (userId === null) {
      alert('Please log in first.');
      return;
    }

    window.location.href = 'checkout.php';
  }
</script>


</body>
</html>
