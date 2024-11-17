<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request OTP</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <main></main>
    <button class="back-button" onclick="window.location.href='login.php'"></button>
    <div class="form-box">
        <h2>Request OTP</h2>
        <form action="generate_otp.php" method="post">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required>
            <input type="submit" value="Request OTP">
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
</body>
</html>
