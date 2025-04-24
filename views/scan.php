<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Tenzin Nargee">
  <title>FastRewards - Scan</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="styles.css">
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
    
    .scan-icon {
      font-size: 5rem;
      color: #6c757d;
      position: relative;
    }
    
    .stars {
      position: absolute;
      top: -10px;
      right: -10px;
      font-size: 1.5rem;
      color: #ffc107;
    }
    
    .file-upload-container {
      border: 2px dashed #ddd;
      border-radius: 10px;
      padding: 30px;
      text-align: center;
      background-color: #f8f9fa;
      margin-bottom: 20px;
    }
    
    .file-upload-container:hover {
      border-color: #007bff;
      background-color: #f1f8ff;
    }
    
    #filePreview {
      max-width: 100%;
      max-height: 300px;
      margin-top: 20px;
      border-radius: 5px;
      display: none;
    }
    
    #cameraPreview {
      width: 100%;
      max-height: 300px;
      border-radius: 5px;
      display: none;
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
        <a href="index.php?command=scan" class="btn btn-outline-secondary me-2" title="Scan">
          <i class="fas fa-camera"></i>
        </a>
        <a href="index.php?command=order" class="btn btn-outline-secondary me-2" title="Order">
          <i class="fas fa-shopping-cart"></i>
        </a>
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
          <li><a href="index.php?command=scan" class="d-block py-1 active">Scan</a></li>
          <li><a href="index.php?command=rewards" class="d-block py-1">Rewards</a></li>
          <li><a href="index.php?command=transfer" class="d-block py-1">Transfer</a></li>
          <li><a href="index.php?command=transactions" class="d-block py-1">Transactions</a></li>
          <li><a href="index.php?command=order" class="d-block py-1">Order</a></li>
          <li><a href="index.php?command=orders_history" class="d-block py-1">Order History</a></li>
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'developer'): ?>
          <li><a href="index.php?command=manage_deals" class="d-block py-1">Manage Deals</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </header>

  <!-- Main Content Wrapper -->
  <div id="wrapper" class="d-flex">
    <nav id="sidebar" class="bg-white border-end d-none d-lg-block">
      <div class="list-group list-group-flush">
        <a href="index.php?command=home" class="list-group-item list-group-item-action">Home</a>
        <a href="index.php?command=scan" class="list-group-item list-group-item-action active">Scan</a>
        <a href="index.php?command=rewards" class="list-group-item list-group-item-action">Rewards</a>
        <a href="index.php?command=transfer" class="list-group-item list-group-item-action">Transfer</a>
        <a href="index.php?command=transactions" class="list-group-item list-group-item-action">Transactions</a>
        <a href="index.php?command=order" class="list-group-item list-group-item-action">Order</a>
        <a href="index.php?command=orders_history" class="list-group-item list-group-item-action">Order History</a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'developer'): ?>
        <a href="index.php?command=manage_deals" class="list-group-item list-group-item-action">Manage Deals</a>
        <?php endif; ?>
      </div>
    </nav>

    <div id="page-content-wrapper">
      <div class="container-fluid scan-content">
        <div class="text-center mb-4">
          <div class="scan-icon mb-3">
            <i class="fas fa-qrcode"></i>
            <div class="stars">★★★★★</div>
          </div>
          <h4 class="mb-4">Upload a receipt image or scan a QR code to earn points.</h4>
        </div>
        
        <?php if(isset($_SESSION['message'])): ?>
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <!-- Restaurant Dropdown -->
        <div class="row justify-content-center mb-4">
          <div class="col-md-6">
            <form id="uploadForm" action="index.php?command=process_file_upload" method="post" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="restaurantSelect" class="form-label">Select Restaurant</label>
                <select class="form-select" id="restaurantSelect" name="restaurant_id" required>
                  <option value="" selected disabled>Choose a restaurant</option>
                  <option value="1">McDonald's</option>
                  <option value="2">Chipotle</option>
                  <option value="3">Starbucks</option>
                  <option value="4">Wawa</option>
                </select>
              </div>
              
              <!-- File Upload Container -->
              <div class="file-upload-container" id="dropZone">
                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                <h5>Drag & Drop Your Receipt Here</h5>
                <p class="text-muted">or</p>
                <div>
                  <input type="file" id="fileInput" name="receipt" accept="image/jpeg,image/png,image/jpg,application/pdf" class="d-none">
                  <button type="button" class="btn btn-primary" id="browseButton">Browse Files</button>
                </div>
                <img id="filePreview" class="mt-3" alt="Preview">
              </div>
              
              <!-- Upload Button -->
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success" id="uploadButton" disabled>Upload Receipt</button>
              </div>
            </form>
          </div>
        </div>
        
        <!-- Camera Section -->
        <div class="row justify-content-center">
          <div class="col-md-6">
            <div id="cameraSection" class="text-center" style="display:none;">
              <div class="mb-3">
                <video id="cameraPreview" autoplay></video>
              </div>
              <div class="d-grid gap-2">
                <button class="btn btn-primary" id="captureButton">Capture</button>
                <button class="btn btn-secondary" id="cancelButton">Cancel</button>
              </div>
            </div>
            
            <div class="d-grid">
              <button class="btn btn-success btn-lg" id="camera-btn">Open Camera</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // File upload preview
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const uploadButton = document.getElementById('uploadButton');
    const browseButton = document.getElementById('browseButton');
    const dropZone = document.getElementById('dropZone');
    const restaurantSelect = document.getElementById('restaurantSelect');
    
    // Camera variables
    const cameraBtn = document.getElementById('camera-btn');
    const cameraSection = document.getElementById('cameraSection');
    const cameraPreview = document.getElementById('cameraPreview');
    const captureButton = document.getElementById('captureButton');
    const cancelButton = document.getElementById('cancelButton');
    let stream;
    
    // Handle file selection
    fileInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const file = this.files[0];
        
        if (file.type.match('image.*')) {
          const reader = new FileReader();
          
          reader.onload = function(e) {
            filePreview.src = e.target.result;
            filePreview.style.display = 'block';
            uploadButton.disabled = !isFormValid();
          };
          
          reader.readAsDataURL(file);
        } else {
          // For non-image files like PDFs
          filePreview.style.display = 'none';
          uploadButton.disabled = !isFormValid();
        }
      }
    });
    
    // Open file browser when the browse button is clicked
    browseButton.addEventListener('click', function() {
      fileInput.click();
    });
    
    // Check if form is valid
    function isFormValid() {
      return fileInput.files.length > 0 && restaurantSelect.value !== '';
    }
    
    // Restaurant dropdown change event
    restaurantSelect.addEventListener('change', function() {
      uploadButton.disabled = !isFormValid();
    });
    
    // Drag and drop functionality
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
      e.preventDefault();
      e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
      dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
      dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
      dropZone.classList.add('bg-light');
    }
    
    function unhighlight() {
      dropZone.classList.remove('bg-light');
    }
    
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
      const dt = e.dataTransfer;
      const files = dt.files;
      
      if (files.length > 0) {
        fileInput.files = files;
        const event = new Event('change');
        fileInput.dispatchEvent(event);
      }
    }
    
    // Camera functionality
    cameraBtn.addEventListener('click', function() {
      if (cameraSection.style.display === 'none') {
        cameraSection.style.display = 'block';
        cameraBtn.style.display = 'none';
        startCamera();
      }
    });
    
    function startCamera() {
      if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
          .then(function(mediaStream) {
            stream = mediaStream;
            cameraPreview.srcObject = mediaStream;
            cameraPreview.style.display = 'block';
            cameraPreview.play();
          })
          .catch(function(error) {
            console.error('Could not access camera: ', error);
            alert('Could not access your camera. Please check permissions.');
            closeCamera();
          });
      } else {
        alert('Your browser does not support camera access.');
        closeCamera();
      }
    }
    
    function closeCamera() {
      if (stream) {
        stream.getTracks().forEach(track => {
          track.stop();
        });
      }
      
      cameraPreview.style.display = 'none';
      cameraSection.style.display = 'none';
      cameraBtn.style.display = 'block';
    }
    
    captureButton.addEventListener('click', function() {
      // This would normally capture an image from the video stream
      // For this demo, we're just closing the camera
      alert('Image captured! In a real app, this would save the image.');
      closeCamera();
    });
    
    cancelButton.addEventListener('click', closeCamera);
  </script>
</body>
</html> 