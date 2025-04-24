<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="Kyle Vitayanuvatti">
    <title>FastRewards - Rewards</title>

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Font Awesome for icons -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="rewards.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            background-color: #ffffff;
        }

        #wrapper {
            height: calc(100vh - 56px); /* Subtract header height */
            overflow: hidden;
        }

        #sidebar {
            height: 100%;
            overflow-y: auto;
            background-color: #ffffff;
        }

        #page-content-wrapper {
            height: 100%;
            overflow-y: auto;
            padding: 20px;
        }

        .section-title {
            margin: 30px 0 20px;
            color: #333;
            font-weight: 600;
        }

        .current-points-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.2s;
        }

        .current-points-card:hover {
            transform: translateY(-5px);
        }

        .current-points-card .brand-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .reward-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            overflow: hidden;
            transition: transform 0.2s;
            cursor: pointer;
        }

        .reward-card:hover {
            transform: translateY(-5px);
        }

        .reward-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .reward-info {
            padding: 15px;
        }

        .reward-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .restaurant-logo {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            object-fit: contain;
        }

        .reward-title {
            margin: 0;
            font-size: 1.1rem;
            color: #333;
        }

        .reward-points {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
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
        <button class="btn btn-outline-secondary me-2" type="button" title="Camera">
          <i class="fas fa-camera"></i>
        </button>
        <button class="btn btn-outline-secondary me-2" type="button" title="Cart">
          <i class="fas fa-shopping-cart"></i>
        </button>
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
          <li><a href="index.php?command=home" class="d-block py-1 active">Home</a></li>
          <li><a href="index.php?command=scan" class="d-block py-1">Scan</a></li>
          <li><a href="index.php?command=rewards" class="d-block py-1">Rewards</a></li>
          <li><a href="index.php?command=transfer" class="d-block py-1">Transfer</a></li>
          <li><a href="index.php?command=transactions" class="d-block py-1">Transactions</a></li>
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'developer'): ?>
          <li><a href="index.php?command=manage_deals" class="d-block py-1">Manage Deals</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </header>

    <!-- Main Content Wrapper -->
    <div id="wrapper" class="d-flex">
      <!-- Sidebar for large screens -->
      <nav id="sidebar" class="bg-white border-end d-none d-lg-block">
        <div class="list-group list-group-flush">
          <a href="index.php?command=home" class="list-group-item list-group-item-action"
            >Home</a
          >
          <a href="index.php?command=scan" class="list-group-item list-group-item-action"
            >Scan</a
          >
          <a
            href="index.php?command=rewards"
            class="list-group-item list-group-item-action active"
            >Rewards</a
          >
          <a href="index.php?command=transfer" class="list-group-item list-group-item-action"
            >Transfer</a
          >
          <a
            href="index.php?command=transactions"
            class="list-group-item list-group-item-action"
            >Transactions</a
          >
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'developer'): ?>
          <a href="index.php?command=manage_deals" class="list-group-item list-group-item-action">Manage Deals</a>
          <?php endif; ?>
        </div>
      </nav>

      <!-- Page Content -->
      <div id="page-content-wrapper">
        <div class="container-fluid rewards-content">
          <!-- Current Points Row -->
          <h4 class="mb-3">Current Points</h4>
          <div class="row mb-4">
            <?php foreach ($pointBalances as $restaurantId => $balance): ?>
            <div class="col-md-3 mb-3">
              <div class="current-points-card">
                <img
                  src="<?php echo htmlspecialchars($balance['logo_path']); ?>"
                  alt="<?php echo htmlspecialchars($balance['restaurant_name']); ?> Logo"
                  class="brand-logo"
                />
                <?php echo htmlspecialchars($balance['points']); ?> Pts
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <!-- Deals Section -->
          <h4 class="section-title">Deals</h4>
          <div class="row">
            <?php 
            $deals = $this->getDeals();
            foreach ($deals as $deal): 
            ?>
            <div class="col-md-3 mb-4">
              <div class="reward-card">
                <img src="<?php echo htmlspecialchars($deal['image_path']); ?>" alt="<?php echo htmlspecialchars($deal['title']); ?>" class="reward-image">
                <div class="reward-info">
                  <div class="reward-header">
                    <img src="<?php echo htmlspecialchars($deal['logo_path']); ?>" alt="<?php echo htmlspecialchars($deal['restaurant_name']); ?> Logo" class="restaurant-logo">
                    <h5 class="reward-title"><?php echo htmlspecialchars($deal['title']); ?></h5>
                  </div>
                  <p class="reward-points"><?php echo htmlspecialchars($deal['points_required']); ?> points</p>
                  <?php if (!empty($deal['description'])): ?>
                  <p class="reward-description"><?php echo htmlspecialchars($deal['description']); ?></p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (count($deals) === 0): ?>
            <div class="col-12 text-center">
              <p>No deals available at the moment.</p>
            </div>
            <?php endif; ?>
          </div>

          <!-- Meals Section -->
          <h4 class="section-title">Meals</h4>
          <div class="row">
            <div class="col-md-3 mb-4">
              <div class="reward-card">
                <img src="assets/rewards/happy-meal.jpeg" alt="Happy Meal" class="reward-image">
                <div class="reward-info">
                  <div class="reward-header">
                    <img src="assets/mcd.png" alt="McDonald's Logo" class="restaurant-logo">
                    <h5 class="reward-title">Happy Meal</h5>
                  </div>
                  <p class="reward-points">5000 points</p>
                </div>
              </div>
            </div>
            <div class="col-md-3 mb-4">
              <div class="reward-card">
                <img src="assets/rewards/bowl.jpg" alt="Chicken Bowl" class="reward-image">
                <div class="reward-info">
                  <div class="reward-header">
                    <img src="assets/chipotle.png" alt="Chipotle Logo" class="restaurant-logo">
                    <h5 class="reward-title">Chicken Bowl</h5>
                  </div>
                  <p class="reward-points">3000 points</p>
                </div>
              </div>
            </div>
            <div class="col-md-3 mb-4">
              <div class="reward-card">
                <img src="assets/rewards/big-mac.png" alt="Big Mac Meal" class="reward-image">
                <div class="reward-info">
                  <div class="reward-header">
                    <img src="assets/mcd.png" alt="McDonald's Logo" class="restaurant-logo">
                    <h5 class="reward-title">Big Mac Meal</h5>
                  </div>
                  <p class="reward-points">5000 points</p>
                </div>
              </div>
            </div>
            <div class="col-md-3 mb-4">
              <div class="reward-card">
                <img src="assets/rewards/burito.jpg" alt="Burrito" class="reward-image">
                <div class="reward-info">
                  <div class="reward-header">
                    <img src="assets/chipotle.png" alt="Chipotle Logo" class="restaurant-logo">
                    <h5 class="reward-title">Burrito</h5>
                  </div>
                  <p class="reward-points">2500 points</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Drinks & Dessert Section -->
          <h4 class="section-title">Drinks & Dessert</h4>
          <div class="row">
            <div class="col-md-3 mb-4">
              <div class="reward-card">
                <img src="assets/rewards/matcha.jpg" alt="Iced Matcha Latte" class="reward-image">
                <div class="reward-info">
                  <div class="reward-header">
                    <img src="assets/starbucks.png" alt="Starbucks Logo" class="restaurant-logo">
                    <h5 class="reward-title">Iced Matcha Latte</h5>
                  </div>
                  <p class="reward-points">700 points</p>
                </div>
              </div>
            </div>
            <div class="col-md-3 mb-4">
              <div class="reward-card">
                <img src="assets/rewards/dessert-deal.jpg" alt="Breakfast Dessert Set" class="reward-image">
                <div class="reward-info">
                  <div class="reward-header">
                    <img src="assets/starbucks.png" alt="Starbucks Logo" class="restaurant-logo">
                    <h5 class="reward-title">Breakfast Dessert Set</h5>
                  </div>
                  <p class="reward-points">1000 points</p>
                </div>
              </div>
            </div>
            <div class="col-md-3 mb-4">
              <div class="reward-card">
                <img src="assets/rewards/sprite.jpeg" alt="Large Sprite" class="reward-image">
                <div class="reward-info">
                  <div class="reward-header">
                    <img src="assets/mcd.png" alt="McDonald's Logo" class="restaurant-logo">
                    <h5 class="reward-title">Large Sprite</h5>
                  </div>
                  <p class="reward-points">500 points</p>
                </div>
              </div>
            </div>
            <div class="col-md-3 mb-4">
              <div class="reward-card">
                <img src="assets/rewards/ice-bevs.png" alt="Iced Coffee" class="reward-image">
                <div class="reward-info">
                  <div class="reward-header">
                    <img src="assets/wawa.png" alt="Wawa Logo" class="restaurant-logo">
                    <h5 class="reward-title">Iced Coffee</h5>
                  </div>
                  <p class="reward-points">2500 points</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html> 
</html> 