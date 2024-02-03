<?php
// Your database connection code here
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['schedule_id'])) {
    $schedule_id = $_POST['schedule_id'];
    $room_id = $_POST['room_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Additional validation and sanitization can be added here

    // Update the meeting in the database
    $updateQuery = "UPDATE schedules SET room_id = '$room_id', start_time = '$start_time', end_time = '$end_time' WHERE schedule_id = '$schedule_id'";
 if ($conn->query($updateQuery) === TRUE) {
        echo "<script>alert('Meeting updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating meeting: " . $conn->error . "');</script>";
    }
} else {
    // Handle the case where meeting_id is not provided
    echo "<script>alert('Invalid request.');</script>";
}

// Close connection
$conn->close();
?>
