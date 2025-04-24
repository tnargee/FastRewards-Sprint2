<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FastRewards - Order Confirmation</title>

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
    
    .confirmation-card {
      border-radius: 10px;
      box-shadow: 0 2px 15px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    
    .order-item {
      border-bottom: 1px solid #eee;
      padding: 10px 0;
    }
    
    .success-icon {
      font-size: 4rem;
      color: #28a745;
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
          <li><a href="index.php?command=order" class="d-block py-1">Order</a></li>
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
        <a href="index.php?command=order" class="list-group-item list-group-item-action">Order</a>
        <a href="index.php?command=orders_history" class="list-group-item list-group-item-action">Order History</a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'developer'): ?>
        <a href="index.php?command=manage_deals" class="list-group-item list-group-item-action">Manage Deals</a>
        <?php endif; ?>
      </div>
    </nav>

    <div id="page-content-wrapper">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-md-8">
            <?php if(isset($_SESSION['message'])): ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            
            <div class="confirmation-card bg-white p-4 mb-4">
              <div class="text-center mb-4">
                <i class="fas fa-check-circle success-icon"></i>
                <h3 class="mt-3">Order Confirmed!</h3>
                <p class="text-muted">Thank you for your order.</p>
              </div>
              
              <div class="d-flex align-items-center mb-4">
                <img src="<?php echo htmlspecialchars($order['logo_path']); ?>" alt="<?php echo htmlspecialchars($order['restaurant_name']); ?> Logo" style="width: 40px; height: 40px; object-fit: contain; margin-right: 10px;">
                <h5 class="mb-0"><?php echo htmlspecialchars($order['restaurant_name']); ?></h5>
              </div>
              
              <div class="mb-4">
                <h6>Order #<?php echo $order['id']; ?></h6>
                <p class="text-muted">Placed on <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></p>
                <p><span class="badge bg-success"><?php echo ucfirst($order['status']); ?></span></p>
              </div>
              
              <div class="mb-4">
                <h6>Order Items</h6>
                <?php foreach($items as $item): ?>
                <div class="order-item">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <span class="fw-bold"><?php echo htmlspecialchars($item['item_name']); ?></span>
                      <?php if(!empty($item['deal_title'])): ?>
                      <small class="d-block text-muted">Deal: <?php echo htmlspecialchars($item['deal_title']); ?></small>
                      <?php endif; ?>
                    </div>
                    <div class="text-end">
                      <span><?php echo '$' . number_format($item['price'], 2); ?></span>
                      <small class="d-block text-muted">Qty: <?php echo $item['quantity']; ?></small>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
              
              <div class="border-top pt-3 mb-4">
                <div class="d-flex justify-content-between mb-2">
                  <span>Subtotal:</span>
                  <span><?php echo '$' . number_format($order['total_amount'] / 1.07, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span>Tax:</span>
                  <span><?php echo '$' . number_format($order['total_amount'] - ($order['total_amount'] / 1.07), 2); ?></span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="fw-bold">Total:</span>
                  <span class="fw-bold"><?php echo '$' . number_format($order['total_amount'], 2); ?></span>
                </div>
              </div>
              
              <div class="border-top pt-3 mb-3">
                <div class="d-flex justify-content-between">
                  <span class="fw-bold">Points Earned:</span>
                  <span class="fw-bold text-success">+<?php echo number_format($order['points_earned']); ?> points</span>
                </div>
              </div>
              
              <div class="d-grid gap-2">
                <a href="index.php?command=order" class="btn btn-primary">Place Another Order</a>
                <a href="index.php?command=orders_history" class="btn btn-outline-secondary">View Order History</a>
        <a href="index.php?command=manage_menu_items" class="list-group-item list-group-item-action">Manage Menu Items</a>
      </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 