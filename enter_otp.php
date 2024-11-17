<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
</head>
<main>
    <body>
        <div class="form-box">
            <h2>Verify OTP</h2>
            <form action="verify_otp.php" method="POST">
                <label for="otp">Enter the OTP sent to your email:</label>
                <input type="text" name="otp" id="otp" required>
                <input type="submit" value="Enter OTP">
            </form><br>
            <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['error'] ? 'error-message' : 'success-message'; ?>">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
            <?php endif; ?>
        </div>
    </body>
</main>
</html>
