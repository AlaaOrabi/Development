<?php
// Your database connection code here
// Your database connection code here
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "room_scheduler";
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Fetch all scheduled meetings
$meetingsQuery = "SELECT * FROM schedules";
$meetingsResult = $conn->query($meetingsQuery);

// Fetch all meeting rooms
$roomsQuery = "SELECT * FROM meeting_rooms";
$roomsResult = $conn->query($roomsQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['schedule_id'])) {
    $schedule_id  = $_POST['schedule_id'];

    // Fetch meeting details
    $meetingQuery = "SELECT * FROM schedules WHERE 	schedule_id= '$schedule_id'";
    $meetingResult = $conn->query($meetingQuery);

    if ($meetingResult->num_rows > 0) {
        $meeting = $meetingResult->fetch_assoc();

        // Fetch all meeting rooms
        $roomsQuery = "SELECT * FROM meeting_rooms";
        $roomsResult = $conn->query($roomsQuery);

        // Fetch all sectors
        $sectorsQuery = "SELECT * FROM sectors";
        $sectorsResult = $conn->query($sectorsQuery);

        // Fetch all administrations
        $adminsQuery = "SELECT * FROM administrations";
        $adminsResult = $conn->query($adminsQuery);
		
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

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Meeting</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Meeting</h2>
    <hr>

    <form method="post" action="update_meeting.php">
        <input type="hidden" name="schedule_id" value="<?php echo $meeting['schedule_id']; ?>">

        <div class="form-group">
            <label for="room_id">Room:</label>
            <select id="room_id" name="room_id" class="form-control" required>
                <?php
                while ($room = $roomsResult->fetch_assoc()) {
                    $selected = ($room['room_id'] == $meeting['room_id']) ? 'selected' : '';
                    echo "<option value='{$room['room_id']}' $selected>{$room['room_name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="Persons">Persons:</label>
            <input type="number" id="persons" name="persons" class="form-control" value="<?php echo $meeting['persons']; ?>" required>
        </div>

        <div class="form-group">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $meeting['start_date']; ?>" required>
        </div>

     <div class="form-group">
            <label for="start_time">Start Time:</label>
            <select id="start_time" name="start_time" class="form-control" value="<?php echo $meeting['start_time']; ?>" required>
                <?php
                // Generate time options for start_time using the saved value
               // $start_time_options = generateTimeOptions($meeting['start_time']);
                echo $meeting['start_time'];
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="end_time">End Time:</label>
            <select id="end_time" name="end_time" class="form-control" value="<?php echo $meeting['end_time']; ?>" required>
                <?php
                // Generate time options for end_time using the saved value
                $end_time_options = generateTimeOptions($meeting['end_time']);
                echo $end_time_options;
                ?>
            </select>
        </div>

	<?php
function generateTimeOptions($selectedTime)
{
    $start_time = strtotime('09:00 AM');
    $end_time = strtotime('05:00 PM');
    $interval = 30 * 60; // 30 minutes in seconds
    $options = '';

    while ($start_time <= $end_time) {
        $formatted_time = date('h:i A', $start_time);
        $selected = ($formatted_time == $selectedTime) ? 'selected' : '';
        $options .= "<option value='{$formatted_time}' $selected>{$formatted_time}</option>";
        $start_time += $interval;
    }

    return $options;
}
?>

        <div class="form-group">
            <label for="user_name">User:</label>
			
            <input type="text" id="user_name" name="user_name" class="form-control" value="<?php echo $meeting['user_name']; ?>" required>
        </div>

        <div class="form-group">
            <label for="sector_id">Sector:</label>
            <select id="sector_id" name="sector_id" class="form-control" required>
                <?php
                while ($sector = $sectorsResult->fetch_assoc()) {
                    $selected = ($sector['sector_id'] == $meeting['sector_id']) ? 'selected' : '';
                    echo "<option value='{$sector['sector_id']}' $selected>{$sector['sector_name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="admin_id">Admin:</label>
            <select id="admin_id" name="admin_id" class="form-control" required>
                <?php
                while ($admin = $adminsResult->fetch_assoc()) {
                    $selected = ($admin['admin_id'] == $meeting['admin_id']) ? 'selected' : '';
                    echo "<option value='{$admin['admin_id']}' $selected>{$admin['admin_name']}</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Meeting</button>
    </form>
</div>

<!-- Bootstrap JS and jQuery (optional, for some Bootstrap features) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
