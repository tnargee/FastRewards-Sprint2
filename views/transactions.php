<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Tenzin Nargee, Kyle Vitayanuvatti">
  <title>FastRewards - Transactions</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="styles.css">
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
          <li><a href="index.php?command=home" class="d-block py-1">Home</a></li>
          <li><a href="index.php?command=scan" class="d-block py-1">Scan</a></li>
          <li><a href="index.php?command=rewards" class="d-block py-1">Rewards</a></li>
          <li><a href="index.php?command=transfer" class="d-block py-1">Transfer</a></li>
          <li><a href="index.php?command=transactions" class="d-block py-1 active">Transactions</a></li>
        </ul>
      </div>
    </div>
  </header>

  <!-- Main Content Wrapper -->
  <div id="wrapper" class="d-flex">
    <!-- Sidebar for large screens -->
    <nav id="sidebar" class="bg-white border-end d-none d-lg-block">
      <div class="list-group list-group-flush">
        <a href="index.php?command=home" class="list-group-item list-group-item-action">Home</a>
        <a href="index.php?command=scan" class="list-group-item list-group-item-action">Scan</a>
        <a href="index.php?command=rewards" class="list-group-item list-group-item-action">Rewards</a>
        <a href="index.php?command=transfer" class="list-group-item list-group-item-action">Transfer</a>
        <a href="index.php?command=transactions" class="list-group-item list-group-item-action active">Transactions</a>
      </div>
    </nav>

    <!-- Page Content -->
    <div id="page-content-wrapper">
      <div class="container-fluid">
        <h2 class="mt-3 mb-4">Transaction History</h2>
        
        <!-- Transaction Filter Controls -->
        <div class="row mb-4">
          <div class="col-md-6">
            <div class="input-group">
              <input type="text" class="form-control" id="searchTransactions" placeholder="Search transactions...">
              <button class="btn btn-outline-secondary" type="button" id="searchButton">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
          <div class="col-md-3">
            <select class="form-select" id="transaction-filter">
              <option value="all">All Transactions</option>
              <option value="transfer">Points Transfer</option>
              <option value="earn">Points Earned</option>
              <option value="redeem">Points Redeemed</option>
            </select>
          </div>
          <div class="col-md-3">
            <button id="refreshTransactions" class="btn btn-outline-primary me-2">
              <i class="fas fa-sync-alt"></i>
            </button>
            <button id="getJsonData" class="btn btn-outline-secondary">
              <i class="fas fa-code me-1"></i> View as JSON
            </button>
          </div>
        </div>
        
        <!-- JSON Data Display -->
        <div id="jsonDisplay" class="mb-4" style="display: none;">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">JSON Response</h5>
              <button type="button" class="btn-close" id="closeJsonDisplay" aria-label="Close"></button>
            </div>
            <div class="card-body">
              <pre id="jsonData" class="mb-0" style="max-height: 300px; overflow-y: auto;"></pre>
            </div>
          </div>
        </div>

        <!-- Transactions List - Will be populated by JavaScript -->
        <div id="transactions-list" class="transactions-container">
          <?php if (empty($transactions)): ?>
            <p class="text-center">No transactions found.</p>
          <?php else: ?>
            <!-- Initial server-rendered transactions that will be replaced by AJAX -->
            <?php foreach ($transactions as $transaction): ?>
            <div class="transaction-card">
              <div class="transaction-icon transfer-icon"></div>
              <div class="transaction-details">
                <h3><?php echo htmlspecialchars($transaction['from_restaurant_name'] ?? ''); ?> → <?php echo htmlspecialchars($transaction['to_restaurant_name'] ?? ''); ?></h3>
                <p class="transaction-date"><?php echo date('M d, Y, h:i A', strtotime($transaction['created_at'])); ?></p>
              </div>
              <div class="transaction-points">
                <p class="points transfer">Transferred: <?php echo htmlspecialchars($transaction['points_transferred']); ?> points</p>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        
        <div class="text-center mt-4 mb-5">
          <button id="loadMoreTransactions" class="btn btn-outline-primary">Load More</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap Bundle JS  -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- jQuery for AJAX -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <!-- Our JavaScript files -->
  <script src="js/validation.js"></script>
  <script src="js/dom-manipulator.js"></script>
  <script src="js/ajax.js"></script>
  <script src="js/app.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize search functionality
      const searchInput = document.getElementById('searchTransactions');
      const searchButton = document.getElementById('searchButton');
      
      function performSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        const transactions = document.querySelectorAll('.transaction-card');
        
        transactions.forEach(card => {
          const text = card.textContent.toLowerCase();
          if (text.includes(searchTerm)) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        });
      }
      
      searchButton.addEventListener('click', performSearch);
      searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
          performSearch();
        }
      });
      
      // JSON display handling
      const getJsonDataBtn = document.getElementById('getJsonData');
      const jsonDisplay = document.getElementById('jsonDisplay');
      const closeJsonDisplay = document.getElementById('closeJsonDisplay');
      
      getJsonDataBtn.addEventListener('click', () => {
        toggleElementVisibility('jsonDisplay');
        
        if (jsonDisplay.style.display !== 'none') {
          // Load JSON data
          fetch('index.php?command=getTransactionsJson')
            .then(response => response.json())
            .then(data => {
              document.getElementById('jsonData').textContent = JSON.stringify(data, null, 2);
            })
            .catch(error => {
              console.error('Error fetching JSON data:', error);
              showNotification('Failed to load JSON data', 'error');
            });
        }
      });
      
      closeJsonDisplay.addEventListener('click', () => {
        jsonDisplay.style.display = 'none';
      });
      
      // Refresh transactions button
      const refreshButton = document.getElementById('refreshTransactions');
      refreshButton.addEventListener('click', () => {
        // Show loading animation
        showNotification('Refreshing transactions...', 'info');
        
        // Use our AJAX function to load transactions
        loadTransactions();
      });
      
      // Load more transactions button
      const loadMoreButton = document.getElementById('loadMoreTransactions');
      let currentPage = 1;
      
      loadMoreButton.addEventListener('click', () => {
        currentPage++;
        
        // Simulate loading more transactions
        loadMoreButton.textContent = 'Loading...';
        loadMoreButton.disabled = true;
        
        // Show loading animation
        setTimeout(() => {
          // This would normally fetch more data from the server
          // For now, we'll simulate it
          
          loadMoreButton.textContent = 'Load More';
          loadMoreButton.disabled = false;
          
          // Check if we have transactions to load
          if (currentPage > 3) {
            loadMoreButton.textContent = 'No more transactions';
            loadMoreButton.disabled = true;
            showNotification('All transactions have been loaded', 'info');
          }
        }, 1000);
      });
      
      // Load transactions on page load
      window.addEventListener('load', loadTransactions);
    });
  </script>
</body>
</html> 