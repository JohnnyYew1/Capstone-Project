<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>Register</title>
</head>
<body>
    <main>
        <form action="registerhandler.php" method="post">
            <div class="form-box">
                <h2>Register</h2>
                
                <!-- Full Name Field -->
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" placeholder="Your full name..." required>

                <!-- Email Field -->
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Your email..." required>

                <!-- Phone Number Field -->
                <label for="phoneNumber">Phone Number</label>
                <input type="text" id="phoneNumber" name="phoneNumber" placeholder="Your phone number..." required>

                <!-- Password Field with Toggle Icon -->
                <label for="Passkey">Password</label>
                <div class="password-container">
                    <input type="password" id="Passkey" name="Passkey" placeholder="Your password..." required>
                    <i id="togglePassword" class="fa fa-eye"></i> <!-- Eye icon for toggle -->
                </div>

                <input type="submit" value="Register">

                <div class="links">
                    <p>Already have an account? <a href="login.php">Log In</a></p>
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
    </main>

    <!-- JavaScript for the toggle functionality -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#Passkey');

        togglePassword.addEventListener('click', function () {
            // Toggle the password visibility
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Toggle the icon
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
