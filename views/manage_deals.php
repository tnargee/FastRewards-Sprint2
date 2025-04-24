<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="Kyle Vitayanuvatti">
    <title>FastRewards - Manage Deals</title>

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

        .manage-deals-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .deal-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
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
          <li><a href="index.php?command=manage_deals" class="d-block py-1 active">Manage Deals</a></li>
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
          <a href="index.php?command=manage_deals" class="list-group-item list-group-item-action active">Manage Deals</a>
        </div>
      </nav>

      <!-- Page Content -->
      <div id="page-content-wrapper">
        <div class="container-fluid">
          <h2 class="mb-4">Manage Deals</h2>
          
          <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          
          <div class="manage-deals-card">
            <h4>Add New Deal</h4>
            <form action="index.php?command=process_deal" method="post">
              <input type="hidden" name="deal_action" value="add">
              
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
                  <label for="title" class="form-label">Deal Title</label>
                  <input type="text" class="form-control" name="title" id="title" required>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="points_required" class="form-label">Points Required</label>
                  <input type="number" class="form-control" name="points_required" id="points_required" min="1" required>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="image_path" class="form-label">Image Path</label>
                  <input type="text" class="form-control" name="image_path" id="image_path" required placeholder="assets/rewards/image.jpg">
                </div>
                
                <div class="col-md-12 mb-3">
                  <label for="description" class="form-label">Description (Optional)</label>
                  <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                </div>
              </div>
              
              <button type="submit" class="btn btn-primary">Add Deal</button>
            </form>
          </div>
          
          <h4 class="mt-4 mb-3">Current Deals</h4>
          
          <?php if(empty($deals)): ?>
            <p>No deals found. Add your first deal above.</p>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Restaurant</th>
                    <th>Points</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($deals as $deal): ?>
                    <tr>
                      <td>
                        <img src="<?php echo htmlspecialchars($deal['image_path']); ?>" alt="<?php echo htmlspecialchars($deal['title']); ?>" class="deal-preview">
                      </td>
                      <td><?php echo htmlspecialchars($deal['title']); ?></td>
                      <td><?php echo htmlspecialchars($deal['restaurant_name']); ?></td>
                      <td><?php echo htmlspecialchars($deal['points_required']); ?></td>
                      <td>
                        <?php if($deal['active']): ?>
                          <span class="badge bg-success">Active</span>
                        <?php else: ?>
                          <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <button type="button" class="btn btn-sm btn-outline-primary edit-deal-btn" 
                                data-id="<?php echo $deal['id']; ?>"
                                data-restaurant="<?php echo $deal['restaurant_id']; ?>"
                                data-title="<?php echo htmlspecialchars($deal['title']); ?>"
                                data-points="<?php echo $deal['points_required']; ?>"
                                data-image="<?php echo htmlspecialchars($deal['image_path']); ?>"
                                data-description="<?php echo htmlspecialchars($deal['description'] ?? ''); ?>"
                                data-active="<?php echo $deal['active'] ? '1' : '0'; ?>"
                                data-bs-toggle="modal" data-bs-target="#editDealModal">
                          Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-deal-btn" 
                                data-id="<?php echo $deal['id']; ?>"
                                data-title="<?php echo htmlspecialchars($deal['title']); ?>"
                                data-bs-toggle="modal" data-bs-target="#deleteDealModal">
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
    
    <!-- Edit Deal Modal -->
    <div class="modal fade" id="editDealModal" tabindex="-1" aria-labelledby="editDealModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editDealModalLabel">Edit Deal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="index.php?command=process_deal" method="post">
            <div class="modal-body">
              <input type="hidden" name="deal_action" value="edit">
              <input type="hidden" name="deal_id" id="edit_deal_id">
              
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
                  <label for="edit_title" class="form-label">Deal Title</label>
                  <input type="text" class="form-control" name="title" id="edit_title" required>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="edit_points_required" class="form-label">Points Required</label>
                  <input type="number" class="form-control" name="points_required" id="edit_points_required" min="1" required>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="edit_image_path" class="form-label">Image Path</label>
                  <input type="text" class="form-control" name="image_path" id="edit_image_path" required>
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
    
    <!-- Delete Deal Modal -->
    <div class="modal fade" id="deleteDealModal" tabindex="-1" aria-labelledby="deleteDealModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteDealModalLabel">Confirm Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the deal: <span id="delete_deal_title"></span>?</p>
            <p class="text-danger">This action cannot be undone.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <form action="index.php?command=process_deal" method="post">
              <input type="hidden" name="deal_action" value="delete">
              <input type="hidden" name="deal_id" id="delete_deal_id">
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
        document.querySelectorAll('.edit-deal-btn').forEach(function(button) {
          button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const restaurant = this.getAttribute('data-restaurant');
            const title = this.getAttribute('data-title');
            const points = this.getAttribute('data-points');
            const image = this.getAttribute('data-image');
            const description = this.getAttribute('data-description');
            const active = this.getAttribute('data-active') === '1';
            
            document.getElementById('edit_deal_id').value = id;
            document.getElementById('edit_restaurant_id').value = restaurant;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_points_required').value = points;
            document.getElementById('edit_image_path').value = image;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_active').checked = active;
          });
        });
        
        // Handle delete buttons
        document.querySelectorAll('.delete-deal-btn').forEach(function(button) {
          button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');
            
            document.getElementById('delete_deal_id').value = id;
            document.getElementById('delete_deal_title').textContent = title;
          });
        });
      });
    </script>
  </body>
</html> 