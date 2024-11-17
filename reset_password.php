<?php  
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome CDN -->
    <style>
        /* Styling for the password container with the icon inside */
        .password-container {
            position: relative;
            width: 100%;
        }
        
        .password-container input {
            width: 100%;
            padding-right: 30px; /* Add space for the icon inside */
            padding: 10px;
            font-size: 16px;
        }

        .password-container i {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<body>
    <main>
    <div class="form-box">  
        <h2>Reset Password</h2>
        <form action="reset_password_handler.php" method="POST">
            <label for="new_password">New Password (min. 8 characters):</label>
            <div class="password-container">
                <input type="password" name="new_password" id="new_password" minlength="8" required>
                <i id="toggleNewPassword" class="fas fa-eye"></i> <!-- Password visibility toggle -->
            </div>
            
            <label for="confirm_password">Confirm New Password:</label>
            <div class="password-container">
                <input type="password" name="confirm_password" id="confirm_password" minlength="8" required>
                <i id="toggleConfirmPassword" class="fas fa-eye"></i> <!-- Password visibility toggle -->
            </div>
            
            <input type="submit" value="Reset Password">
        </form><br>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['error'] ? 'error-message' : 'success-message'; ?>">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
    </div>
    </main>

    <script>
        // Toggle password visibility for new password field
        const toggleNewPassword = document.getElementById('toggleNewPassword');
        const newPasswordField = document.getElementById('new_password');
        
        toggleNewPassword.addEventListener('click', function() {
            const type = newPasswordField.type === 'password' ? 'text' : 'password';
            newPasswordField.type = type;
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Toggle password visibility for confirm password field
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordField = document.getElementById('confirm_password');
        
        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordField.type === 'password' ? 'text' : 'password';
            confirmPasswordField.type = type;
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>