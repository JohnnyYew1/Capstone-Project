<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome CDN -->
    <title>Login</title>
</head>
<body>
    <main>
    <form action="loginhandler.php" method="post">
    <div class="form-box">
        <h2>Log In</h2>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Your email..." required>

        <label for="Passkey">Password</label>
        <div class="password-container">
            <input type="password" id="Passkey" name="loginPasskey" placeholder="Your Password..." required>
            <i id="togglePassword" class="fas fa-eye"></i>
        </div>

        <input type="submit" value="Sign In">

        <!-- Links section: Register and Forgot Password aligned to the right -->
        <div class="links">
            <p>Don't have an account? <a href="register.php">Register</a></p>
            <p><a href="request_otp.php" class="forgot-password">Forgot Password</a></p>
        </div>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['error'] ? 'error-message' : 'success-message'; ?>">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
    </div>
</form>

<script>
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('Passkey');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
</script>
</body>
</html>
