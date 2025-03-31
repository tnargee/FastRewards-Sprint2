<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Tenzin Nargee">
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
        <a href="index.php?command=transfer" class="list-group-item list-group-item-action">Transfer</a>
        <a href="index.php?command=transactions" class="list-group-item list-group-item-action active">Transactions</a>
      </div>
    </nav>

    <div id="page-content-wrapper">
      <div class="container-fluid">
        <h1 class="mt-4">Transaction History</h1>
        
        <!-- Transaction Filters -->
        <div class="row mb-4">
          <div class="col-md-3">
            <select class="form-select">
              <option value="">All Restaurants</option>
              <option value="mcd">McDonald's</option>
              <option value="chipotle">Chipotle</option>
              <option value="starbucks">Starbucks</option>
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-select">
              <option value="">All Types</option>
              <option value="earn">Points Earned</option>
              <option value="redeem">Points Redeemed</option>
              <option value="transfer">Points Transferred</option>
            </select>
          </div>
          <div class="col-md-3">
            <input type="date" class="form-control" placeholder="Start Date">
          </div>
          <div class="col-md-3">
            <input type="date" class="form-control" placeholder="End Date">
          </div>
        </div>

        <!-- Transaction List -->
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Restaurant</th>
                    <th>Type</th>
                    <th>Points</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>2025-08-20</td>
                    <td>McDonald's</td>
                    <td>Transfer</td>
                    <td class="text-danger">-500</td>
                    <td>Transferred to Chipotle</td>
                  </tr>
                  <tr>
                    <td>2025-08-19</td>
                    <td>Starbucks</td>
                    <td>Earned</td>
                    <td class="text-success">+100</td>
                    <td>Purchased coffee</td>
                  </tr>
                  <tr>
                    <td>2025-08-18</td>
                    <td>Chipotle</td>
                    <td>Redeemed</td>
                    <td class="text-danger">-200</td>
                    <td>Redeemed for free burrito</td>
                  </tr>
                  <tr>
                    <td>2025-08-17</td>
                    <td>McDonald's</td>
                    <td>Earned</td>
                    <td class="text-success">+250</td>
                    <td>Purchased meal</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 