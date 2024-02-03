<?php
session_start(); // Start the session

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login_page.php");
    exit();
}

// Your database connection code here
include_once("connection.php"); // Assuming you have a separate file for database connection

// Check if the meeting_id parameter is set in the URL
if (isset($_GET['schedule_id'])) {
    $schedule_id = $_GET['schedule_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Form submitted, update the meeting details
        $meeting_title = $_POST['meeting_title'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $persons = $_POST['persons'];
        $room_id = $_POST['room_id'];
        $start_date = $_POST['start_date'];
        // Add more fields as needed

        // Update the meeting in the database
        $updateMeetingQuery = "UPDATE schedules SET 
            meeting_title = '$meeting_title',
            start_time = '$start_time',
            end_time = '$end_time',
            persons = '$persons',
            room_id = '$room_id',
            start_date = '$start_date'
            WHERE schedule_id = $schedule_id AND user_id = {$_SESSION['user_id']}";

        if ($conn->query($updateMeetingQuery) === TRUE) {
            // Meeting updated successfully, redirect to the user dashboard
            header("Location: user_dashboard.php");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        // Fetch meeting details for editing
        $editMeetingQuery = "
            SELECT s.*, CONCAT(m.room_name, ', ', m.location) AS room_details
            FROM schedules s
            INNER JOIN meeting_rooms m ON s.room_id = m.room_id
            WHERE s.schedule_id = $schedule_id
            AND s.user_id = {$_SESSION['user_id']}";

        $editMeetingResult = $conn->query($editMeetingQuery);

        // Check if the meeting exists
        if ($editMeetingResult->num_rows > 0) {
            $meetingDetails = $editMeetingResult->fetch_assoc();
        } else {
            // Redirect to the user dashboard if the meeting doesn't exist or doesn't belong to the user
            header("Location: user_dashboard.php");
            exit();
        }

        // Fetch available meeting rooms for dropdown
        $meetingRoomsQuery = "SELECT room_id, CONCAT(room_name, ', ', location) AS room_details FROM meeting_rooms";
        $meetingRoomsResult = $conn->query($meetingRoomsQuery);
        $meetingRooms = [];

        if ($meetingRoomsResult->num_rows > 0) {
            while ($row = $meetingRoomsResult->fetch_assoc()) {
                $meetingRooms[$row['room_id']] = $row['room_details'];
            }
        }
    }
} else {
    // Redirect to the user dashboard if the meeting_id parameter is not set
    header("loadFloors(): user_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meeting</title>
    <!-- Bootstrap CSS link -->
    <link rel="stylesheet" href="css/edit.css"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h2 class="text-center"> تعديل حجز قاعة</h2>
                </div>
                <div class="card-body">
                    <!-- Your HTML form for editing -->
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?schedule_id=$schedule_id"); ?>">
                        <!-- Include input fields for meeting details -->
                        <div class="form-group">
                            <label for="meeting_title">عنوان الأجتماع :</label>
                            <input type="text" id="meeting_title" name="meeting_title" class="form-control" value="<?php echo $meetingDetails['meeting_title']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="start_time">بداية الأجتماع :</label>
                            <input type="time" id="start_time" name="start_time" class="form-control" value="<?php echo $meetingDetails['start_time']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="end_time">نهاية الأجتماع :</label>
                            <input type="time" id="end_time" name="end_time" class="form-control" value="<?php echo $meetingDetails['end_time']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="persons">عدد الحاضرين:</label>
                            <input type="number" id="persons" name="persons" class="form-control" value="<?php echo $meetingDetails['persons']; ?>" required>
                        </div>
                         <div class="form-group">
                            <label for="room_id">رقم الغرفة :</label>
                            <select id="room_id" name="room_id" class="form-control select2" required>
                                <?php
                                foreach ($meetingRooms as $roomId => $roomDetails) {
                                    $selected = ($roomId == $meetingDetails['room_id']) ? 'selected' : '';
                                    echo "<option value='$roomId' $selected>$roomDetails</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_date">تاريخ الأجتماع :</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $meetingDetails['start_date']; ?>" required>
                        </div>
                        <!-- ... Add more input fields as needed ... -->

                        <button type="submit" class="btn btn-warning btn-block"> حفظ التعديل</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and jQuery scripts (place them at the end of the body for better performance) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Select2 JS script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>



</body>
</html>
