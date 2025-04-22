<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Tenzin Nargee, Kyle Vitayanuvatti">
  <title>FastRewards - Transfer Points</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="transfer.css">
  <link rel="stylesheet" href="js-styles.css">

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
        
        <div id="validation-errors" class="alert alert-danger" style="display: none;"></div>
        
        <section class="transfer-section mb-4">
          <form action="index.php?command=processTransfer" method="POST" id="transfer-form">
            <!-- Add hidden fields for state maintenance -->
            <input type="hidden" name="previous_from_restaurant" value="<?php echo isset($_POST['from_restaurant']) ? htmlspecialchars($_POST['from_restaurant']) : ''; ?>">
            <input type="hidden" name="previous_to_restaurant" value="<?php echo isset($_POST['to_restaurant']) ? htmlspecialchars($_POST['to_restaurant']) : ''; ?>">
            <input type="hidden" name="previous_amount" value="<?php echo isset($_POST['points']) ? htmlspecialchars($_POST['points']) : ''; ?>">
            <input type="hidden" name="transfer_count" value="<?php echo isset($_POST['transfer_count']) ? intval($_POST['transfer_count']) + 1 : 1; ?>">
            
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
                  <span class="balance-<?php echo htmlspecialchars($firstRestaurantId); ?>">
                    <?php echo htmlspecialchars($pointBalances[$firstRestaurantId]['points'] ?? 0); ?> pts
                  </span>
                </p>
                
                <label for="amount" class="form-label">Enter Transfer Amount:</label>
                <input type="number" class="form-control" id="amount" name="points" placeholder="Amount" required min="1">
              </div>

              <div class="col-md-4 transfer-col d-flex flex-column justify-content-center">
                <p class="conversion-rate mb-2" id="conversionRate">Conversion Rate: 1:0.5</p>
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
                  <span class="balance-<?php echo htmlspecialchars($secondRestaurantId); ?>">
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
              Get Transactions JSON
            </button>
          </div>
          
          <div id="jsonResponse" class="mt-3 p-3 bg-light" style="display: none;">
            <pre id="jsonData"></pre>
          </div>
        </section>
      </div>
    </div>
  </div>
  
  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- jQuery for AJAX -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <!-- Our JavaScript files -->
  <script src="js/validation.js"></script>
  <script src="js/dom-manipulator.js"></script>
  <script src="js/ajax.js"></script>
  <script src="js/app.js"></script>
  
  <script>
    // Transfer form dynamic calculations
    document.addEventListener('DOMContentLoaded', function() {
      const fromRestaurant = document.getElementById('fromRestaurant');
      const toRestaurant = document.getElementById('toRestaurant');
      const transferAmount = document.getElementById('amount');
      const fromPoints = document.getElementById('fromPoints').querySelector('span');
      const toPoints = document.getElementById('toPoints').querySelector('span');
      const newBalance = document.getElementById('newBalance').querySelector('span');
      const receivedPoints = document.getElementById('receivedPoints');
      const fromRestaurantLogo = document.getElementById('fromRestaurantLogo').querySelector('img');
      const toRestaurantLogo = document.getElementById('toRestaurantLogo').querySelector('img');
      
      // Calculate points on input change
      function calculatePoints() {
        const amount = parseInt(transferAmount.value) || 0;
        const conversionRate = 0.5; // 50% conversion rate
        const received = Math.floor(amount * conversionRate);
        
        receivedPoints.textContent = `You will receive ${received} points`;
        
        // Update new balance preview
        const currentToPoints = parseInt(toPoints.textContent) || 0;
        newBalance.textContent = `${currentToPoints + received} pts`;
        
        // Validate that user has enough points
        const currentFromPoints = parseInt(fromPoints.textContent) || 0;
        if (amount > currentFromPoints) {
          transferAmount.classList.add('invalid-input');
          showNotification('You don\'t have enough points for this transfer', 'error');
        } else {
          transferAmount.classList.remove('invalid-input');
        }
      }
      
      // Update restaurant logos and point balances when selections change
      function updateFromRestaurant() {
        const selectedOption = fromRestaurant.options[fromRestaurant.selectedIndex];
        const logo = selectedOption.getAttribute('data-logo');
        const points = selectedOption.getAttribute('data-points');
        
        fromRestaurantLogo.src = logo;
        fromPoints.textContent = `${points} pts`;
        fromPoints.className = `balance-${fromRestaurant.value}`;
        
        // Update toRestaurant options to disable the selected fromRestaurant
        Array.from(toRestaurant.options).forEach(option => {
          option.disabled = option.value === fromRestaurant.value;
        });
        
        calculatePoints();
      }
      
      function updateToRestaurant() {
        const selectedOption = toRestaurant.options[toRestaurant.selectedIndex];
        const logo = selectedOption.getAttribute('data-logo');
        const points = selectedOption.getAttribute('data-points');
        
        toRestaurantLogo.src = logo;
        toPoints.textContent = `${points} pts`;
        toPoints.className = `balance-${toRestaurant.value}`;
        
        calculatePoints();
      }
      
      // Add event listeners
      fromRestaurant.addEventListener('change', updateFromRestaurant);
      toRestaurant.addEventListener('change', updateToRestaurant);
      transferAmount.addEventListener('input', calculatePoints);
      
      // Initialize points calculation
      calculatePoints();
      
      // AJAX for getting transaction data
      const getJsonDataBtn = document.getElementById('getJsonData');
      const jsonResponse = document.getElementById('jsonResponse');
      const jsonData = document.getElementById('jsonData');
      
      getJsonDataBtn.addEventListener('click', function() {
        // Toggle visibility of JSON response area
        if (jsonResponse.style.display === 'none') {
          // Fetch transactions data
          fetch('index.php?command=getTransactionsJson')
            .then(response => response.json())
            .then(data => {
              jsonData.textContent = JSON.stringify(data, null, 2);
              toggleElementVisibility('jsonResponse');
            })
            .catch(error => {
              console.error('Error fetching transaction data:', error);
              showNotification('Failed to load transaction data', 'error');
            });
        } else {
          toggleElementVisibility('jsonResponse');
        }
      });
    });

    $(document).ready(function() {
        $('#transfer-form').on('submit', function(e) {
            e.preventDefault();
            
            const fromRestaurant = $('#from_restaurant').val();
            const toRestaurant = $('#to_restaurant').val();
            const points = $('#points').val();
            
            if (!fromRestaurant || !toRestaurant || !points) {
                showNotification('Please fill in all fields', 'error');
                return;
            }
            
            transferPoints(fromRestaurant, toRestaurant, points);
        });
        
        // Update conversion rate when restaurants or points change
        $('#from_restaurant, #to_restaurant, #points').on('change', function() {
            updateConversionRate();
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