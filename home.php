<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 1) {
    header("Location: login.php");
    exit();
}

$user_fullname = $_SESSION['fullName'] ?? "User";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Home - Shuttle Booking</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="navbar.css">
</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <img src="LogoInti.png" alt="Shuttle Logo">
        </div>
        <ul>
            <li><a href="booking.php">Book Shuttle</a></li>
            <li><a href="announcements.php">Announcements</a></li>
            <li><a href="booked.php">Booked</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <section class="hero">
            <div class="welcome">
                <h1>Welcome, <?php echo $user_fullname; ?>!</h1>
                <p>Plan your day by booking a shuttle and stay updated with the latest announcements.</p>
            </div>
            <div class="hero-image">
                <img src="shuttle1.jpeg" alt="Shuttle Bus1">
            </div>
        </section>

        <section class="content">
            <div class="booking">
                <h2>Book Your Shuttle</h2>
                <p>Choose from one of our pre-determined routes and convenient pickup points.</p>
                <a href="booking.php" class="cta-button">Book Now</a>
            </div>

            <div class="announcements">
                <h2>Latest Announcements</h2>
                <p>Stay up-to-date with important updates regarding shuttle services.</p>
                <a href="announcements.php" class="cta-button">View Announcements</a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Shuttle Booking System. All rights reserved.</p>
    </footer>
</body>
</html>
