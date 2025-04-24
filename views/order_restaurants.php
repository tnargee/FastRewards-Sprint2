<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FastRewards - Order</title>

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
    
    .restaurant-card {
      transition: transform 0.2s;
      cursor: pointer;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .restaurant-card:hover {
      transform: translateY(-5px);
    }
    
    .restaurant-logo {
      width: 100%;
      height: 150px;
      object-fit: contain;
      padding: 20px;
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
        <h3 class="mb-4">Choose a Restaurant to Order From</h3>
        
        <?php if(isset($_SESSION['message'])): ?>
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <div class="row">
          <?php foreach ($restaurants as $restaurant): ?>
            <div class="col-md-3 mb-4">
              <div class="restaurant-card" onclick="window.location.href='index.php?command=order&restaurant_id=<?php echo $restaurant['id']; ?>'">
                <img src="<?php echo htmlspecialchars($restaurant['logo_path']); ?>" class="restaurant-logo" alt="<?php echo htmlspecialchars($restaurant['name']); ?> Logo">
                <div class="p-3">
                  <h5 class="text-center"><?php echo htmlspecialchars($restaurant['name']); ?></h5>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 