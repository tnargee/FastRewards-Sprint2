<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="author" content="Kyle Vitayanuvatti">
    <title>FastRewards - Sign In</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="sign-up.css" />
  </head>
  <body>
    <header class="top-header">
      <h1>FastRewards</h1>
    </header>

    <main class="signup-page">
      <section class="signup-container">
        <h2>Sign In</h2>

        <?php if (isset($message) && !empty($message)): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="index.php?command=processSignIn" method="post">
          <!-- Email field -->
          <div class="form-field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" placeholder="Email" required />
          </div>

          <!-- Password field -->
          <div class="form-field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Password" required />
          </div>

          <!-- Sign in button -->
          <button type="submit" class="sign-up-button">Sign In</button>
        </form>

        <div class="divider-row">
          <hr />
          <span>or</span>
          <hr />
        </div>

        <button class="google-button">Continue with Google</button>

        <p class="signin-link">
          Don't have an account? <a href="index.php?command=signup">Sign Up</a>
        </p>
      </section>
    </main>
  </body>
</html> 