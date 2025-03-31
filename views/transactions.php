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
    <nav id="sidebar" class="bg-light border-end d-none d-lg-block">
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
            <select class="form-select" id="filterType">
              <option value="">All Transactions</option>
              <option value="points-transfer">Points Transfer</option>
              <option value="points-earned">Points Earned</option>
              <option value="points-redeemed">Points Redeemed</option>
            </select>
          </div>
          <div class="col-md-3">
            <button id="getJsonData" class="btn btn-outline-secondary w-100">
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

        <!-- Transactions Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th>Transaction Type</th>
                <th>From</th>
                <th>To</th>
                <th>Points</th>
                <th>Received</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($transactions)): ?>
                <tr>
                  <td colspan="6" class="text-center">No transactions found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                  <td><?php echo date('m/d/Y', strtotime($transaction['created_at'])); ?></td>
                  <td>Points Transfer</td>
                  <td>
                    <?php if($transaction['from_restaurant_id']): ?>
                      <img src="<?php echo htmlspecialchars($transaction['from_logo_path']); ?>" alt="Restaurant Logo" height="20">
                      <?php echo htmlspecialchars($transaction['from_restaurant_name']); ?>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($transaction['to_restaurant_id']): ?>
                      <img src="<?php echo htmlspecialchars($transaction['to_logo_path']); ?>" alt="Restaurant Logo" height="20">
                      <?php echo htmlspecialchars($transaction['to_restaurant_name']); ?>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td><?php echo htmlspecialchars($transaction['points_transferred']); ?></td>
                  <td><?php echo htmlspecialchars($transaction['points_received']); ?></td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap Bundle JS  -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // JSON data handling
      const getJsonDataBtn = document.getElementById('getJsonData');
      const jsonDisplay = document.getElementById('jsonDisplay');
      const closeJsonDisplay = document.getElementById('closeJsonDisplay');
      const jsonData = document.getElementById('jsonData');
      
      getJsonDataBtn.addEventListener('click', function() {
        // Toggle JSON display
        if (jsonDisplay.style.display === 'none') {
          // Fetch JSON data from API
          fetch('index.php?command=getTransactionsJson')
            .then(response => response.json())
            .then(data => {
              jsonData.textContent = JSON.stringify(data, null, 2);
              jsonDisplay.style.display = 'block';
            })
            .catch(error => {
              jsonData.textContent = 'Error fetching data: ' + error;
              jsonDisplay.style.display = 'block';
            });
        } else {
          jsonDisplay.style.display = 'none';
        }
      });
      
      closeJsonDisplay.addEventListener('click', function() {
        jsonDisplay.style.display = 'none';
      });
      
      // Simple transaction search functionality
      const searchInput = document.getElementById('searchTransactions');
      const searchButton = document.getElementById('searchButton');
      const filterType = document.getElementById('filterType');
      const tableRows = document.querySelectorAll('tbody tr');
      
      function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const filterValue = filterType.value.toLowerCase();
        
        tableRows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const transactionType = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
          
          const matchesSearch = searchTerm === '' || text.includes(searchTerm);
          const matchesFilter = filterValue === '' || transactionType.includes(filterValue);
          
          row.style.display = matchesSearch && matchesFilter ? '' : 'none';
        });
      }
      
      searchButton.addEventListener('click', filterTable);
      searchInput.addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
          filterTable();
        }
      });
      
      filterType.addEventListener('change', filterTable);
    });
  </script>
</body>
</html> 