<?php
session_start();

include 'navigation.php';
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 1) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM announcements 
          WHERE Status = 1 
          AND (UserType = 1 OR UserType = 0)
          AND (ScheduledDateTime IS NULL OR ScheduledDateTime <= NOW()) 
          ORDER BY ScheduledDateTime DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="announcement.css">
</head>
<body>
    <main>
        <h1>Announcements</h1>
        <section id="announcements-list">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Post Date</th>
                            <th>Scheduled Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Title']); ?></td>
                                <td><?php echo htmlspecialchars($row['Message']); ?></td>
                                <td><?php echo $row['CreatedAt']; ?></td>
                                <td>
                                    <?php 
                                    echo $row['ScheduledDateTime'] 
                                        ? date('Y-m-d H:i', strtotime($row['ScheduledDateTime'])) 
                                        : '--:--:--';
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No announcements available at this time.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
