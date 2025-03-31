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
        <section class="transfer-section mb-4">
          <div class="row text-center">
            <div class="col-md-4 transfer-col">
              <img src="assets/mcd.png" alt="McDonald's Logo" class="transfer-restaurant-logo">
              <select class="form-select mb-2">
                <option>McDonald's</option>
                <option>Chipotle</option>
                <option>Starbucks</option>
              </select>
              <p>Current Balance: 4500 pts</p>
              <label for="transferAmount" class="form-label">Enter Transfer Amount:</label>
              <input type="number" class="form-control" id="transferAmount" placeholder="Amount">
            </div>

            <div class="col-md-4 transfer-col d-flex flex-column justify-content-center">
              <p class="conversion-rate mb-2">Conversion Rate: 1:0.5</p>
              <i class="fas fa-arrow-right fa-3x mb-2"></i>
              <p class="receive-label mb-3">You will receive 250 Chipotle Points</p>
              <button class="btn btn-primary">Transfer</button>
            </div>

            <div class="col-md-4 transfer-col">
              <img src="assets/chipotle.png" alt="Chipotle Logo" class="transfer-restaurant-logo">
              <select class="form-select mb-2">
                <option>Chipotle</option>
                <option>McDonald's</option>
                <option>Starbucks</option>
              </select>
              <p>Current Balance: 300 pts</p>
              <p>New Balance: 550 pts</p>
            </div>
          </div>
        </section>

        <section class="recent-transfers">
          <h5>Recent Transfers</h5>
          <ul class="list-group">
            <li class="list-group-item">08/20/2025: Transferred 500 pts from McDonald's to Chipotle</li>
            <li class="list-group-item">08/15/2025: Transferred 300 pts from McDonald's to Starbucks</li>
            <li class="list-group-item">08/10/2025: Transferred 200 pts from Starbucks to Chipotle</li>
          </ul>
        </section>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 