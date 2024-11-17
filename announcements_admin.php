<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 2) {
    header('Location: login.php');
    exit();
}

$query = "SELECT * FROM announcements ORDER BY CreatedAt DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements</title>
    <link rel="stylesheet" href="user_management.css">
</head>
<body>

    <?php include 'navbar_admin.php'; ?>

    <main>
        <h1>Manage Announcements</h1>
        <section id="announcements-list">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Post Date</th>
                            <th>Scheduled Date & Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Title']); ?></td>
                                <td><?php echo htmlspecialchars($row['Message']); ?></td>
                                <td><?php echo $row['CreatedAt']; ?></td>

                                <td>
                                    <?php
                                    echo $row['ScheduledDateTime'] ? date('Y-m-d H:i', strtotime($row['ScheduledDateTime'])) : '--:--:--';
                                    ?>
                                </td>


                                <td class="<?php echo ($row['Status'] == 1) ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo ($row['Status'] == 1) ? 'Active' : 'Inactive'; ?>
                                </td>
                                <td>
                                    <a href="announcement_status.php?announcement_id=<?php echo $row['AnnouncementID']; ?>&current_status=<?php echo $row['Status']; ?>" 
                                        onclick="return confirm('Are you sure you want to <?php echo ($row['Status'] == 1) ? 'deactivate' : 'activate'; ?> this announcement?');" 
                                        class="<?php echo ($row['Status'] == 1) ? 'deactivate-link' : 'activate-link'; ?>">
                                            <?php echo ($row['Status'] == 1) ? 'Deactivate' : 'Activate'; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No announcements available.</p>
            <?php endif; ?>
        </section>
        <a href="create_announcements.php" class="float-button">
            <img src="https://png.pngtree.com/png-vector/20190214/ourmid/pngtree-vector-plus-icon-png-image_515260.jpg" alt="Create Announcement" class="plus-icon">
        </a>
    </main>

</body>
</html>

<?php
mysqli_close($conn);
?>