<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Announcement</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="announcement.css">
    <script>
        function toggleScheduleFields() {
            const scheduleFields = document.getElementById('schedule-fields');
            const isScheduled = document.getElementById('is_scheduled').checked;
            scheduleFields.style.display = isScheduled ? 'block' : 'none';
            
            // Clear the date and time fields if scheduling is disabled
            if (!isScheduled) {
                document.getElementById('publish_date').value = '';
                document.getElementById('publish_time').value = '';
            }
        }

        function setMinDateTime() {
            const now = new Date();

            const minDate = now.toISOString().split("T")[0];
            document.getElementById('publish_date').setAttribute('min', minDate);
            document.getElementById('publish_date').value = minDate;

            now.setMinutes(now.getMinutes() + 30);
            const minTime = now.toTimeString().split(":").slice(0, 2).join(":");
            document.getElementById('publish_time').setAttribute('min', minTime);
            document.getElementById('publish_time').value = minTime;
        }

        window.onload = setMinDateTime;
    </script>
</head>
<body>
    <?php include 'navbar_admin.php'; ?>
    <main>
        <h2>Create Announcement</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='success-message'>" . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo "<div class='error-message'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>
        <form action="create_announcements_handler.php" method="POST">
            <label for="title">Title:</label>
            <input type="text" name="title" required>
            
            <label for="message">Content:</label>
            <textarea name="message" required></textarea>
            <br><br>

            <label for="is_scheduled">Schedule this post?</label>
            <input type="checkbox" id="is_scheduled" name="is_scheduled" onclick="toggleScheduleFields()">

            <div id="schedule-fields" style="display: none;">
                <label for="publish_date">Publish Date:</label>
                <input type="date" id="publish_date" name="publish_date">

                <label for="publish_time">Publish Time:</label>
                <input type="time" id="publish_time" name="publish_time">
            </div>
            <br>

            <label for="status">Set as Active:</label>
            <input type="checkbox" name="status" value="1">
            <br><br>

            <label for="user_type">Visibility:</label>
            <select id="user_type" name="user_type" required>
                <option value="1">User Only</option>
                <option value="3">Driver Only</option>
                <option value="0">Both</option>
            </select>
            <br><br>

            <input type="submit" value="Create Announcement">
        </form>
    </main>
</body>
</html>
