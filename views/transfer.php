<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Tenzin Nargee">
  <title>FastRewards - Transfer Points</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="transfer.css">
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
        </ul>
      </div>
    </div>
  </header>

  <!-- Main Content Wrapper -->
  <div id="wrapper" class="d-flex">
    <nav id="sidebar" class="bg-light border-end d-none d-lg-block">
      <div class="list-group list-group-flush">
        <a href="index.php?command=home" class="list-group-item list-group-item-action">Home</a>
        <a href="index.php?command=scan" class="list-group-item list-group-item-action">Scan</a>
        <a href="index.php?command=rewards" class="list-group-item list-group-item-action">Rewards</a>
        <a href="index.php?command=transfer" class="list-group-item list-group-item-action active">Transfer</a>
        <a href="index.php?command=transactions" class="list-group-item list-group-item-action">Transactions</a>
      </div>
    </nav>

    <div id="page-content-wrapper">
      <div class="container-fluid transfer-content">
      
        <?php if(isset($message) && !empty($message)): ?>
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        
        <section class="transfer-section mb-4">
          <form action="index.php?command=processTransfer" method="POST" id="transferForm">
            <div class="row text-center">
              <div class="col-md-4 transfer-col">
                <div id="fromRestaurantLogo">
                  <?php 
                    $firstRestaurantId = reset($restaurants)['id'] ?? 0;
                    $firstRestaurantLogo = reset($restaurants)['logo_path'] ?? '';
                    echo '<img src="' . htmlspecialchars($firstRestaurantLogo) . '" alt="Restaurant Logo" class="transfer-restaurant-logo">';
                  ?>
                </div>
                
                <select class="form-select mb-2" name="from_restaurant" id="fromRestaurant" required>
                  <?php foreach($restaurants as $restaurant): ?>
                    <option value="<?php echo htmlspecialchars($restaurant['id']); ?>" 
                            data-logo="<?php echo htmlspecialchars($restaurant['logo_path']); ?>"
                            data-points="<?php echo htmlspecialchars($pointBalances[$restaurant['id']]['points'] ?? 0); ?>">
                      <?php echo htmlspecialchars($restaurant['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                
                <p id="fromPoints">Current Balance: 
                  <span>
                    <?php echo htmlspecialchars($pointBalances[$firstRestaurantId]['points'] ?? 0); ?> pts
                  </span>
                </p>
                
                <label for="transferAmount" class="form-label">Enter Transfer Amount:</label>
                <input type="number" class="form-control" id="transferAmount" name="points" placeholder="Amount" required min="1">
              </div>

              <div class="col-md-4 transfer-col d-flex flex-column justify-content-center">
                <p class="conversion-rate mb-2">Conversion Rate: 1:0.5</p>
                <i class="fas fa-arrow-right fa-3x mb-2"></i>
                <p class="receive-label mb-3" id="receivedPoints">You will receive 0 points</p>
                <button type="submit" class="btn btn-primary">Transfer</button>
              </div>

              <div class="col-md-4 transfer-col">
                <div id="toRestaurantLogo">
                  <?php 
                    $secondRestaurantId = next($restaurants)['id'] ?? 0;
                    $secondRestaurantLogo = current($restaurants)['logo_path'] ?? '';
                    echo '<img src="' . htmlspecialchars($secondRestaurantLogo) . '" alt="Restaurant Logo" class="transfer-restaurant-logo">';
                  ?>
                </div>
                
                <select class="form-select mb-2" name="to_restaurant" id="toRestaurant" required>
                  <?php foreach($restaurants as $restaurant): ?>
                    <option value="<?php echo htmlspecialchars($restaurant['id']); ?>"
                            data-logo="<?php echo htmlspecialchars($restaurant['logo_path']); ?>"
                            data-points="<?php echo htmlspecialchars($pointBalances[$restaurant['id']]['points'] ?? 0); ?>"
                            <?php echo ($restaurant['id'] == $secondRestaurantId) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($restaurant['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                
                <p id="toPoints">Current Balance: 
                  <span>
                    <?php echo htmlspecialchars($pointBalances[$secondRestaurantId]['points'] ?? 0); ?> pts
                  </span>
                </p>
                
                <p id="newBalance">New Balance: <span>0 pts</span></p>
              </div>
            </div>
          </form>
        </section>

        <section class="recent-transfers">
          <h5>Recent Transfers</h5>
          <?php if(empty($recentTransfers)): ?>
            <p>No recent transfers.</p>
          <?php else: ?>
            <ul class="list-group">
              <?php foreach($recentTransfers as $transfer): ?>
                <li class="list-group-item">
                  <?php echo date('m/d/Y', strtotime($transfer['created_at'])); ?>: 
                  Transferred <?php echo htmlspecialchars($transfer['points_transferred']); ?> pts 
                  from <?php echo htmlspecialchars($transfer['from_restaurant_name']); ?> 
                  to <?php echo htmlspecialchars($transfer['to_restaurant_name']); ?>
                  (Received <?php echo htmlspecialchars($transfer['points_received']); ?> pts)
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
          
          <div class="mt-3">
            <a href="index.php?command=transactions" class="btn btn-outline-secondary btn-sm">
              View All Transactions
            </a>
            <button id="getJsonData" class="btn btn-outline-secondary btn-sm">
              Get JSON Data
            </button>
          </div>
          
          <div id="jsonResponse" class="mt-3 p-3 bg-light" style="display: none;">
            <pre id="jsonData"></pre>
          </div>
        </section>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Transfer form dynamic calculations
    document.addEventListener('DOMContentLoaded', function() {
      const fromRestaurant = document.getElementById('fromRestaurant');
      const toRestaurant = document.getElementById('toRestaurant');
      const transferAmount = document.getElementById('transferAmount');
      const fromPoints = document.getElementById('fromPoints').querySelector('span');
      const toPoints = document.getElementById('toPoints').querySelector('span');
      const newBalance = document.getElementById('newBalance').querySelector('span');
      const receivedPoints = document.getElementById('receivedPoints');
      const fromRestaurantLogo = document.getElementById('fromRestaurantLogo');
      const toRestaurantLogo = document.getElementById('toRestaurantLogo');
      const conversionRate = 0.5; // Example conversion rate
      
      function updateLogos() {
        // Update from restaurant logo
        const fromLogo = fromRestaurant.options[fromRestaurant.selectedIndex].getAttribute('data-logo');
        fromRestaurantLogo.innerHTML = `<img src="${fromLogo}" alt="From Restaurant Logo" class="transfer-restaurant-logo">`;
        
        // Update to restaurant logo
        const toLogo = toRestaurant.options[toRestaurant.selectedIndex].getAttribute('data-logo');
        toRestaurantLogo.innerHTML = `<img src="${toLogo}" alt="To Restaurant Logo" class="transfer-restaurant-logo">`;
      }
      
      function updatePointDisplay() {
        // Update points display
        fromPoints.textContent = fromRestaurant.options[fromRestaurant.selectedIndex].getAttribute('data-points') + ' pts';
        toPoints.textContent = toRestaurant.options[toRestaurant.selectedIndex].getAttribute('data-points') + ' pts';
        
        // Calculate received points
        const amount = parseInt(transferAmount.value) || 0;
        const received = Math.floor(amount * conversionRate);
        receivedPoints.textContent = `You will receive ${received} points`;
        
        // Calculate new balance
        const currentToPoints = parseInt(toRestaurant.options[toRestaurant.selectedIndex].getAttribute('data-points')) || 0;
        newBalance.textContent = (currentToPoints + received) + ' pts';
      }
      
      // Add event listeners
      fromRestaurant.addEventListener('change', function() {
        updateLogos();
        updatePointDisplay();
      });
      
      toRestaurant.addEventListener('change', function() {
        updateLogos();
        updatePointDisplay();
      });
      
      transferAmount.addEventListener('input', updatePointDisplay);
      
      // Form validation
      document.getElementById('transferForm').addEventListener('submit', function(e) {
        const fromId = parseInt(fromRestaurant.value);
        const toId = parseInt(toRestaurant.value);
        
        if (fromId === toId) {
          alert('Cannot transfer points to the same restaurant');
          e.preventDefault();
          return false;
        }
        
        const amount = parseInt(transferAmount.value) || 0;
        const availablePoints = parseInt(fromRestaurant.options[fromRestaurant.selectedIndex].getAttribute('data-points')) || 0;
        
        if (amount <= 0) {
          alert('Please enter a positive number of points');
          e.preventDefault();
          return false;
        }
        
        if (amount > availablePoints) {
          alert('Not enough points available for transfer');
          e.preventDefault();
          return false;
        }
        
        return true;
      });
      
      // Initial update
      updateLogos();
      updatePointDisplay();
      
      // JSON data fetching
      document.getElementById('getJsonData').addEventListener('click', function() {
        const jsonResponse = document.getElementById('jsonResponse');
        const jsonData = document.getElementById('jsonData');
        
        // Toggle visibility
        if (jsonResponse.style.display === 'none') {
          // Fetch JSON data
          fetch('index.php?command=getTransactionsJson')
            .then(response => response.json())
            .then(data => {
              jsonData.textContent = JSON.stringify(data, null, 2);
              jsonResponse.style.display = 'block';
            })
            .catch(error => {
              jsonData.textContent = 'Error fetching data: ' + error;
              jsonResponse.style.display = 'block';
            });
        } else {
          jsonResponse.style.display = 'none';
        }
      });
    });
  </script>
</body>
</html> 