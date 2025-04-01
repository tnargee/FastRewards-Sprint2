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
            <!-- Add hidden fields for state maintenance -->
            <input type="hidden" name="previous_from_restaurant" value="<?php echo isset($_POST['from_restaurant']) ? htmlspecialchars($_POST['from_restaurant']) : ''; ?>">
            <input type="hidden" name="previous_to_restaurant" value="<?php echo isset($_POST['to_restaurant']) ? htmlspecialchars($_POST['to_restaurant']) : ''; ?>">
            <input type="hidden" name="previous_amount" value="<?php echo isset($_POST['points']) ? htmlspecialchars($_POST['points']) : ''; ?>">
            <input type="hidden" name="transfer_count" value="<?php echo isset($_POST['transfer_count']) ? intval($_POST['transfer_count']) + 1 : 1; ?>">
            
            <div class="row text-center">
              <div class="col-md-4 transfer-col">
                <div id="fromRestaurantLogo" class="transfer-logo-container">
                  <?php 
                    $firstRestaurantId = reset($restaurants)['id'] ?? 0;
                    $firstRestaurantLogo = reset($restaurants)['logo_path'] ?? '';
                    echo '<img src="' . htmlspecialchars($firstRestaurantLogo) . '" alt="Restaurant Logo" class="transfer-restaurant-logo">';
                  ?>
                </div>
                
                <div class="transfer-controls">
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
              </div>

              <div class="col-md-4 transfer-col d-flex flex-column justify-content-center">
                <p class="conversion-rate mb-2" id="conversionRate">Conversion Rate: 1:0.5</p>
                <i class="fas fa-arrow-right fa-3x mb-2"></i>
                <p class="receive-label mb-3" id="receivedPoints">You will receive 0 points</p>
                <button type="submit" class="btn btn-primary">Transfer</button>
              </div>

              <div class="col-md-4 transfer-col">
                <div id="toRestaurantLogo" class="transfer-logo-container">
                  <?php 
                    $secondRestaurantId = next($restaurants)['id'] ?? 0;
                    $secondRestaurantLogo = current($restaurants)['logo_path'] ?? '';
                    echo '<img src="' . htmlspecialchars($secondRestaurantLogo) . '" alt="Restaurant Logo" class="transfer-restaurant-logo">';
                  ?>
                </div>
                
                <div class="transfer-controls">
                  <select class="form-select mb-2" name="to_restaurant" id="toRestaurant" required>
                    <?php foreach($restaurants as $restaurant): ?>
                      <?php if($restaurant['id'] != $firstRestaurantId): ?>
                        <option value="<?php echo htmlspecialchars($restaurant['id']); ?>"
                                data-logo="<?php echo htmlspecialchars($restaurant['logo_path']); ?>"
                                data-points="<?php echo htmlspecialchars($pointBalances[$restaurant['id']]['points'] ?? 0); ?>"
                                <?php echo ($restaurant['id'] == $secondRestaurantId) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($restaurant['name']); ?>
                        </option>
                      <?php endif; ?>
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
      const conversionRate = document.getElementById('conversionRate');
      const fromLogo = document.getElementById('fromRestaurantLogo');
      const toLogo = document.getElementById('toRestaurantLogo');

      // Define conversion rates between restaurants
      const conversionRates = {
        '1-2': 0.5,  // McDonald's to Chipotle
        '2-1': 2.0,  // Chipotle to McDonald's
        '1-3': 0.75, // McDonald's to Starbucks
        '3-1': 1.33, // Starbucks to McDonald's
        '1-4': 0.6,  // McDonald's to Wawa
        '4-1': 1.67, // Wawa to McDonald's
        '2-3': 1.5,  // Chipotle to Starbucks
        '3-2': 0.67, // Starbucks to Chipotle
        '2-4': 1.2,  // Chipotle to Wawa
        '4-2': 0.83, // Wawa to Chipotle
        '3-4': 0.8,  // Starbucks to Wawa
        '4-3': 1.25  // Wawa to Starbucks
      };

      function updateToRestaurantOptions() {
        const fromId = fromRestaurant.value;
        const currentToId = toRestaurant.value;
        
        // Clear current options
        toRestaurant.innerHTML = '';
        
        // Add new options excluding the selected "from" restaurant
        Array.from(fromRestaurant.options).forEach(option => {
          if (option.value !== fromId) {
            const newOption = document.createElement('option');
            newOption.value = option.value;
            newOption.dataset.logo = option.dataset.logo;
            newOption.dataset.points = option.dataset.points;
            newOption.textContent = option.textContent;
            toRestaurant.appendChild(newOption);
          }
        });
        
        // If the current "to" restaurant was the same as the new "from" restaurant,
        // select the first available option
        if (currentToId === fromId) {
          toRestaurant.selectedIndex = 0;
        }
        
        updateLogos();
      }

      function updateConversionRate() {
        const fromId = fromRestaurant.value;
        const toId = toRestaurant.value;
        const rate = conversionRates[`${fromId}-${toId}`] || 1;
        
        conversionRate.textContent = `Conversion Rate: 1:${rate}`;
        updateReceivedPoints(rate);
      }

      function updateReceivedPoints(rate) {
        const amount = parseInt(transferAmount.value) || 0;
        const received = Math.floor(amount * rate);
        receivedPoints.textContent = `You will receive ${received} points`;
        
        const currentToPoints = parseInt(toPoints.textContent);
        newBalance.textContent = `${currentToPoints + received} pts`;
      }

      function updateLogos() {
        const fromOption = fromRestaurant.options[fromRestaurant.selectedIndex];
        const toOption = toRestaurant.options[toRestaurant.selectedIndex];
        
        fromLogo.innerHTML = `<img src="${fromOption.dataset.logo}" alt="Restaurant Logo" class="transfer-restaurant-logo">`;
        toLogo.innerHTML = `<img src="${toOption.dataset.logo}" alt="Restaurant Logo" class="transfer-restaurant-logo">`;
        
        fromPoints.textContent = `${fromOption.dataset.points} pts`;
        toPoints.textContent = `${toOption.dataset.points} pts`;
        
        updateConversionRate();
      }

      fromRestaurant.addEventListener('change', updateToRestaurantOptions);
      toRestaurant.addEventListener('change', updateLogos);
      transferAmount.addEventListener('input', function() {
        const fromId = fromRestaurant.value;
        const toId = toRestaurant.value;
        const rate = conversionRates[`${fromId}-${toId}`] || 1;
        updateReceivedPoints(rate);
      });

      // Initial update
      updateToRestaurantOptions();
      
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

  <style>
    .transfer-logo-container {
      height: 200px;  /* Increased from 150px */
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
    }

    .transfer-restaurant-logo {
      max-height: 100%;
      max-width: 100%;
      object-fit: contain;
    }

    .transfer-controls {
      height: 200px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .transfer-col {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
  </style>
</body>
</html> 