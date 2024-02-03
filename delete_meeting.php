<?php
// Your database connection code here
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "room_scheduler";
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['schedule_id'])) {
    $schedule_id = $_POST['schedule_id'];

    // Fetch meeting details
    //$meetingQuery = "SELECT * FROM schedules WHERE schedule_id= '$schedule_id'";
	$meetingQuery = "SELECT s.*, r.room_name, se.sector_name, a.admin_name
                     FROM schedules s
                     JOIN meeting_rooms r ON s.room_id = r.room_id
                     JOIN sectors se ON s.sector_id = se.sector_id
                     JOIN administrations a ON s.admin_id = a.admin_id
                     WHERE s.schedule_id = '$schedule_id'";
    $meetingResult = $conn->query($meetingQuery);

    if ($meetingResult->num_rows > 0) {
        $meeting = $meetingResult->fetch_assoc();
    } else {
        // Handle the case where the meeting is not found
        echo "Meeting not found.";
        exit;
    }
} else {
    // Handle the case where the meeting_id is not provided
    echo "Invalid request.";
    exit;
}
// Fetch all meeting rooms
//$roomsQuery = "SELECT * FROM meeting_rooms";
//$roomsResult = $conn->query($roomsQuery);
// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Delete Meeting</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Delete Meeting</h2>
    <hr>

    <p>Are you sure you want to delete the following meeting?</p>

    <ul>
        <li><strong>Room:</strong> <?php echo $meeting['room_name']; ?></li>
        <li><strong>Persons:</strong> <?php echo $meeting['persons']; ?></li>
        <li><strong>Start Date:</strong> <?php echo $meeting['start_date']; ?></li>
        <li><strong>Start Time:</strong> <?php echo $meeting['start_time']; ?></li>
        <li><strong>End Time:</strong> <?php echo $meeting['end_time']; ?></li>
        <li><strong>User:</strong> <?php echo $meeting['user_name']; ?></li>
        <li><strong>Sector:</strong> <?php echo $meeting['sector_name']; ?></li>
        <li><strong>Admin:</strong> <?php echo $meeting['admin_name']; ?></li>
    </ul>

    <form method="post" action="confirm_delete_meeting.php">
        <input type="hidden" name="schedule_id" value="<?php echo $meeting['schedule_id']; ?>">
        <button type="submit" class="btn btn-danger">Confirm Delete</button>
    </form>
</div>

<!-- Bootstrap JS and jQuery (optional, for some Bootstrap features) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
