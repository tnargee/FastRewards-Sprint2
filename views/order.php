<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FastRewards - Order from <?php echo htmlspecialchars($restaurant['name']); ?></title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      overflow: hidden;
    }

    #wrapper {
      height: calc(100vh - 56px); /* Subtract header height */
      overflow: hidden;
    }

    #sidebar {
      height: 100%;
      overflow-y: auto;
    }

    #page-content-wrapper {
      height: 100%;
      overflow-y: auto;
      padding: 20px;
    }
    
    .item-card {
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      height: 100%;
      display: flex;
      flex-direction: column;
      background-color: white;
    }
    
    .item-card .p-3 {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }
    
    .item-card p {
      flex-grow: 1;
    }
    
    .item-image {
      width: 100%;
      height: 150px;
      object-fit: cover;
    }
    
    .cart-card {
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      position: sticky;
      top: 20px;
      background-color: white;
    }
    
    .cart-item {
      border-bottom: 1px solid #eee;
      padding: 10px 0;
    }
    
    .quantity-control {
      display: flex;
      align-items: center;
    }
    
    .quantity-control button {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .quantity-display {
      width: 30px;
      text-align: center;
    }
    
    #menuItems .col-md-6 {
      margin-bottom: 16px;
    }
  </style>
</head>
<body>
  <!-- Top Navbar -->
  <header class="top-navbar navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid">
      <!-- Hamburger menu for small screens -->
      <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sideMenuDropdown" aria-controls="sideMenuDropdown" aria-expanded="false" aria-label="Toggle navigation" style="margin-right: 5px;">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="navbar-brand" href="#">FastRewards</a>
      <form class="d-flex mx-auto">
        <input class="form-control me-2 search-input" type="search" placeholder="Search..." aria-label="Search">
      </form>
      <div class="d-flex">
        <a href="index.php?command=scan" class="btn btn-outline-secondary me-2" title="Scan">
          <i class="fas fa-camera"></i>
        </a>
        <a href="index.php?command=order" class="btn btn-outline-secondary me-2" title="Order">
          <i class="fas fa-shopping-cart"></i>
        </a>
        <div class="dropdown">
          <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Profile">
            <i class="fas fa-user-circle"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
            <li><span class="dropdown-item-text"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span></li>
            <li><span class="dropdown-item-text text-muted"><?php echo htmlspecialchars($_SESSION['email']); ?></span></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="index.php?command=logout">Sign Out</a></li>
          </ul>
        </div>
      </div>
    </div>
    <!-- Collapsible dropdown for side menu links on small screens -->
    <div class="collapse d-lg-none" id="sideMenuDropdown">
      <div class="bg-white p-2 border-top">
        <ul class="list-unstyled mb-0">
          <li><a href="index.php?command=home" class="d-block py-1">Home</a></li>
          <li><a href="index.php?command=scan" class="d-block py-1">Scan</a></li>
          <li><a href="index.php?command=rewards" class="d-block py-1">Rewards</a></li>
          <li><a href="index.php?command=transfer" class="d-block py-1">Transfer</a></li>
          <li><a href="index.php?command=transactions" class="d-block py-1">Transactions</a></li>
          <li><a href="index.php?command=order" class="d-block py-1 active">Order</a></li>
          <li><a href="index.php?command=orders_history" class="d-block py-1">Order History</a></li>
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'developer'): ?>
          <li><a href="index.php?command=manage_deals" class="d-block py-1">Manage Deals</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </header>

  <!-- Main Content Wrapper -->
  <div id="wrapper" class="d-flex">
    <nav id="sidebar" class="bg-white border-end d-none d-lg-block">
      <div class="list-group list-group-flush">
        <a href="index.php?command=home" class="list-group-item list-group-item-action">Home</a>
        <a href="index.php?command=scan" class="list-group-item list-group-item-action">Scan</a>
        <a href="index.php?command=rewards" class="list-group-item list-group-item-action">Rewards</a>
        <a href="index.php?command=transfer" class="list-group-item list-group-item-action">Transfer</a>
        <a href="index.php?command=transactions" class="list-group-item list-group-item-action">Transactions</a>
        <a href="index.php?command=order" class="list-group-item list-group-item-action active">Order</a>
        <a href="index.php?command=orders_history" class="list-group-item list-group-item-action">Order History</a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'developer'): ?>
        <a href="index.php?command=manage_deals" class="list-group-item list-group-item-action">Manage Deals</a>
        <?php endif; ?>
      </div>
    </nav>

    <div id="page-content-wrapper">
      <div class="container-fluid">
        <div class="d-flex align-items-center mb-4">
          <a href="index.php?command=order" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left"></i> Back
          </a>
          <div class="d-flex align-items-center">
            <img src="<?php echo htmlspecialchars($restaurant['logo_path']); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?> Logo" style="width: 40px; height: 40px; object-fit: contain; margin-right: 10px;">
            <h3 class="mb-0">Order from <?php echo htmlspecialchars($restaurant['name']); ?></h3>
          </div>
        </div>
        
        <?php if(isset($_SESSION['message'])): ?>
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <div class="row">
          <div class="col-md-8">
            <h5 class="mb-3">Menu Items</h5>
            
            <!-- Sample menu items -->
            <div class="row g-3" id="menuItems">
              <div class="col-md-6 mb-3">
                <div class="item-card">
                  <div class="p-3">
                    <h5>Cheeseburger</h5>
                    <p class="text-muted">Classic cheeseburger with lettuce, tomato, and special sauce.</p>
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="fw-bold">$5.99</span>
                      <button class="btn btn-primary btn-sm" onclick="addToCart(1, 'Cheeseburger', 5.99)">Add to Cart</button>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6 mb-3">
                <div class="item-card">
                  <div class="p-3">
                    <h5>French Fries</h5>
                    <p class="text-muted">Golden crispy french fries, perfectly salted.</p>
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="fw-bold">$2.99</span>
                      <button class="btn btn-primary btn-sm" onclick="addToCart(2, 'French Fries', 2.99)">Add to Cart</button>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6 mb-3">
                <div class="item-card">
                  <div class="p-3">
                    <h5>Soda</h5>
                    <p class="text-muted">Refreshing soda of your choice.</p>
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="fw-bold">$1.99</span>
                      <button class="btn btn-primary btn-sm" onclick="addToCart(3, 'Soda', 1.99)">Add to Cart</button>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6 mb-3">
                <div class="item-card">
                  <div class="p-3">
                    <h5>Chicken Nuggets</h5>
                    <p class="text-muted">Crispy chicken nuggets with your choice of sauce.</p>
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="fw-bold">$4.99</span>
                      <button class="btn btn-primary btn-sm" onclick="addToCart(4, 'Chicken Nuggets', 4.99)">Add to Cart</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Rewards/Deals Section -->
            <?php if (!empty($deals)): ?>
              <h5 class="mb-3 mt-4">Deals & Rewards</h5>
              <div class="row g-3">
                <?php foreach ($deals as $deal): ?>
                  <div class="col-md-6 mb-3">
                    <div class="item-card">
                      <img src="<?php echo htmlspecialchars($deal['image_path']); ?>" alt="<?php echo htmlspecialchars($deal['title']); ?>" class="item-image">
                      <div class="p-3">
                        <h5><?php echo htmlspecialchars($deal['title']); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($deal['description']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                          <span class="fw-bold"><?php echo number_format($deal['points_required']); ?> points</span>
                          <button class="btn btn-success btn-sm" 
                                  onclick="addDealToCart(<?php echo $deal['id']; ?>, '<?php echo addslashes($deal['title']); ?>', <?php echo $deal['points_required']; ?>)"
                                  <?php echo ($currentPoints < $deal['points_required']) ? 'disabled' : ''; ?>>
                            <?php echo ($currentPoints < $deal['points_required']) ? 'Not enough points' : 'Redeem'; ?>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="col-md-4">
            <div class="cart-card">
              <div class="p-3 bg-light">
                <h5 class="mb-0">Your Cart</h5>
              </div>
              <div class="p-3">
                <div id="cartItems">
                  <div class="text-center py-4 text-muted">
                    Your cart is empty
                  </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between mb-2">
                  <span>Subtotal:</span>
                  <span id="subtotal">$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span>Tax:</span>
                  <span id="tax">$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                  <span class="fw-bold">Total:</span>
                  <span class="fw-bold" id="total">$0.00</span>
                </div>
                
                <form id="orderForm" action="index.php?command=process_order" method="post">
                  <input type="hidden" name="restaurant_id" value="<?php echo $restaurant['id']; ?>">
                  <input type="hidden" name="items" id="orderItems" value="">
                  <button type="submit" class="btn btn-primary w-100" id="submitOrder" disabled>Place Order</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Cart functionality
    let cart = [];
    const taxRate = 0.07;
    
    function updateCartDisplay() {
      const cartItemsDiv = document.getElementById('cartItems');
      const subtotalSpan = document.getElementById('subtotal');
      const taxSpan = document.getElementById('tax');
      const totalSpan = document.getElementById('total');
      const submitButton = document.getElementById('submitOrder');
      const orderItemsInput = document.getElementById('orderItems');
      
      if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<div class="text-center py-4 text-muted">Your cart is empty</div>';
        subtotalSpan.textContent = '$0.00';
        taxSpan.textContent = '$0.00';
        totalSpan.textContent = '$0.00';
        submitButton.disabled = true;
        return;
      }
      
      let html = '';
      let subtotal = 0;
      
      cart.forEach((item, index) => {
        const itemTotal = (item.price * item.quantity).toFixed(2);
        subtotal += parseFloat(itemTotal);
        
        html += `
          <div class="cart-item">
            <div class="d-flex justify-content-between">
              <span>${item.name}</span>
              <span>$${itemTotal}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
              <div class="quantity-control">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${index}, -1)">-</button>
                <span class="quantity-display">${item.quantity}</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${index}, 1)">+</button>
              </div>
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${index})">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          </div>
        `;
      });
      
      cartItemsDiv.innerHTML = html;
      
      const tax = subtotal * taxRate;
      const total = subtotal + tax;
      
      subtotalSpan.textContent = '$' + subtotal.toFixed(2);
      taxSpan.textContent = '$' + tax.toFixed(2);
      totalSpan.textContent = '$' + total.toFixed(2);
      submitButton.disabled = false;
      
      // Update hidden form field with cart items JSON
      orderItemsInput.value = JSON.stringify(cart);
    }
    
    function addToCart(id, name, price) {
      // Check if item is already in cart
      const existingItemIndex = cart.findIndex(item => item.id === id && !item.isDeal);
      
      if (existingItemIndex !== -1) {
        cart[existingItemIndex].quantity += 1;
      } else {
        cart.push({
          id: id,
          name: name,
          price: price,
          quantity: 1,
          isDeal: false
        });
      }
      
      updateCartDisplay();
    }
    
    function addDealToCart(dealId, name, points) {
      // Add deal to cart - points are stored for reference but cost is $0
      cart.push({
        id: dealId,
        deal_id: dealId,
        name: name,
        price: 0, // Deals are free (paid with points)
        quantity: 1,
        isDeal: true,
        points: points
      });
      
      updateCartDisplay();
    }
    
    function updateQuantity(index, change) {
      cart[index].quantity += change;
      
      if (cart[index].quantity <= 0) {
        removeItem(index);
      } else {
        updateCartDisplay();
      }
    }
    
    function removeItem(index) {
      cart.splice(index, 1);
      updateCartDisplay();
    }
  </script>
</body>
</html> 