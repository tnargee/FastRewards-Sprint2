<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="Kyle Vitayanuvatti">
    <title>FastRewards - Manage Menu Items</title>

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

        .manage-items-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
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
          <li><a href="index.php?command=transactions" class="d-block py-1">Transactions</a></li>
          <li><a href="index.php?command=order" class="d-block py-1">Order</a></li>
          <li><a href="index.php?command=orders_history" class="d-block py-1">Order History</a></li>
          <li><a href="index.php?command=manage_deals" class="d-block py-1">Manage Deals</a></li>
          <li><a href="index.php?command=manage_menu_items" class="d-block py-1 active">Manage Menu Items</a></li>
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
          <a href="index.php?command=transactions" class="list-group-item list-group-item-action">Transactions</a>
          <a href="index.php?command=order" class="list-group-item list-group-item-action">Order</a>
          <a href="index.php?command=orders_history" class="list-group-item list-group-item-action">Order History</a>
          <a href="index.php?command=manage_deals" class="list-group-item list-group-item-action">Manage Deals</a>
          <a href="index.php?command=manage_menu_items" class="list-group-item list-group-item-action active">Manage Menu Items</a>
        </div>
      </nav>

      <!-- Page Content -->
      <div id="page-content-wrapper">
        <div class="container-fluid">
          <h2 class="mb-4">Manage Menu Items</h2>
          
          <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          
          <div class="manage-items-card">
            <h4>Add New Menu Item</h4>
            <form action="index.php?command=process_menu_item" method="post">
              <input type="hidden" name="action" value="add">
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="restaurant_id" class="form-label">Restaurant</label>
                  <select class="form-select" name="restaurant_id" id="restaurant_id" required>
                    <option value="">Select Restaurant</option>
                    <?php foreach($restaurants as $restaurant): ?>
                      <option value="<?php echo $restaurant['id']; ?>"><?php echo htmlspecialchars($restaurant['name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="name" class="form-label">Menu Item Name</label>
                  <input type="text" class="form-control" name="name" id="name" required>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="price" class="form-label">Price</label>
                  <input type="number" class="form-control" name="price" id="price" min="0.01" step="0.01" required>
                </div>
                
                <div class="col-md-12 mb-3">
                  <label for="description" class="form-label">Description (Optional)</label>
                  <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                </div>
              </div>
              
              <button type="submit" class="btn btn-primary">Add Menu Item</button>
            </form>
          </div>
          
          <h4 class="mt-4 mb-3">Current Menu Items</h4>
          
          <?php if(empty($menuItems)): ?>
            <p>No menu items found. Add your first menu item above.</p>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Restaurant</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($menuItems as $item): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($item['name']); ?></td>
                      <td><?php echo htmlspecialchars($item['restaurant_name']); ?></td>
                      <td>$<?php echo number_format($item['price'], 2); ?></td>
                      <td><?php echo htmlspecialchars($item['description'] ?? ''); ?></td>
                      <td>
                        <?php if($item['active']): ?>
                          <span class="badge bg-success">Active</span>
                        <?php else: ?>
                          <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <button type="button" class="btn btn-sm btn-outline-primary edit-item-btn" 
                                data-id="<?php echo $item['id']; ?>"
                                data-restaurant="<?php echo $item['restaurant_id']; ?>"
                                data-name="<?php echo htmlspecialchars($item['name']); ?>"
                                data-price="<?php echo $item['price']; ?>"
                                data-description="<?php echo htmlspecialchars($item['description'] ?? ''); ?>"
                                data-active="<?php echo $item['active'] ? '1' : '0'; ?>"
                                data-bs-toggle="modal" data-bs-target="#editItemModal">
                          Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-item-btn" 
                                data-id="<?php echo $item['id']; ?>"
                                data-name="<?php echo htmlspecialchars($item['name']); ?>"
                                data-bs-toggle="modal" data-bs-target="#deleteItemModal">
                          Delete
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <!-- Edit Menu Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editItemModalLabel">Edit Menu Item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="index.php?command=process_menu_item" method="post">
            <div class="modal-body">
              <input type="hidden" name="action" value="edit">
              <input type="hidden" name="item_id" id="edit_item_id">
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="edit_restaurant_id" class="form-label">Restaurant</label>
                  <select class="form-select" name="restaurant_id" id="edit_restaurant_id" required>
                    <option value="">Select Restaurant</option>
                    <?php foreach($restaurants as $restaurant): ?>
                      <option value="<?php echo $restaurant['id']; ?>"><?php echo htmlspecialchars($restaurant['name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="edit_name" class="form-label">Menu Item Name</label>
                  <input type="text" class="form-control" name="name" id="edit_name" required>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="edit_price" class="form-label">Price</label>
                  <input type="number" class="form-control" name="price" id="edit_price" min="0.01" step="0.01" required>
                </div>
                
                <div class="col-md-12 mb-3">
                  <label for="edit_description" class="form-label">Description (Optional)</label>
                  <textarea class="form-control" name="description" id="edit_description" rows="2"></textarea>
                </div>
                
                <div class="col-md-12 mb-3 form-check form-switch ms-1">
                  <input class="form-check-input" type="checkbox" role="switch" id="edit_active" name="active">
                  <label class="form-check-label" for="edit_active">Active</label>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <!-- Delete Menu Item Modal -->
    <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-labelledby="deleteItemModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteItemModalLabel">Confirm Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the menu item: <span id="delete_item_name"></span>?</p>
            <p class="text-danger">This action cannot be undone.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <form action="index.php?command=process_menu_item" method="post">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="item_id" id="delete_item_id">
              <button type="submit" class="btn btn-danger">Delete</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Handle edit buttons
        document.querySelectorAll('.edit-item-btn').forEach(function(button) {
          button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const restaurant = this.getAttribute('data-restaurant');
            const name = this.getAttribute('data-name');
            const price = this.getAttribute('data-price');
            const description = this.getAttribute('data-description');
            const active = this.getAttribute('data-active') === '1';
            
            document.getElementById('edit_item_id').value = id;
            document.getElementById('edit_restaurant_id').value = restaurant;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_active').checked = active;
          });
        });
        
        // Handle delete buttons
        document.querySelectorAll('.delete-item-btn').forEach(function(button) {
          button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            document.getElementById('delete_item_id').value = id;
            document.getElementById('delete_item_name').textContent = name;
          });
        });
      });
    </script>
  </body>
</html> 