<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="author" content="Kyle Vitayanuvatti">
    <title>FastRewards - Sign Up</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="sign-up.css" />
    <link rel="stylesheet" href="js-styles.css" />
  </head>
  <body>
    <header class="top-header">
      <h1>FastRewards</h1>
    </header>

    <main class="signup-page">
      <section class="signup-container">
        <h2>Sign Up</h2>

        <?php if (isset($message) && !empty($message)): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div id="validation-errors" class="alert alert-danger" style="display: none;"></div>

        <form action="index.php?command=processSignUp" method="post">
          <div class="name-row">
            <div class="form-field">
              <label for="firstName">First Name</label>
              <input
                id="firstName"
                name="firstName"
                type="text"
                placeholder="First Name"
                required
              />
            </div>

            <div class="form-field">
              <label for="lastName">Last Name</label>
              <input
                id="lastName"
                name="lastName"
                type="text"
                placeholder="Last Name"
                required
              />
            </div>
          </div>

          <!-- Email field -->
          <div class="form-field">
            <label for="email">Email</label>
            <input
              id="email"
              name="email"
              type="email"
              placeholder="Email"
              required
            />
          </div>

          <!-- Password field -->
          <div class="form-field">
            <label for="password">Password</label>
            <input
              id="password"
              name="password"
              type="password"
              placeholder="Password"
              required
              minlength="6"
            />
          </div>

          <!-- Sign up button -->
          <button type="submit" class="sign-up-button">Sign Up</button>
        </form>

        <div class="divider-row">
          <hr />
          <span>or</span>
          <hr />
        </div>

        <button class="google-button">Continue with Google</button>

        <p class="signin-link">
          Have an account? <a href="index.php?command=signin">Sign In</a>
        </p>
      </section>
    </main>
    
    <!-- JavaScript files -->
    <script src="js/validation.js"></script>
    <script src="js/dom-manipulator.js"></script>
    <script src="js/app.js"></script>
  </body>
</html> 