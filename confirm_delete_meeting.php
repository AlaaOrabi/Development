<?php
// Your database connection code here
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['schedule_id'])) {
    $schedule_id = $_POST['schedule_id'];

    // Delete the meeting from the database
    $deleteQuery = "DELETE FROM schedules WHERE schedule_id = '$schedule_id'";

    if ($conn->query($deleteQuery) === TRUE) {
        echo "Meeting deleted successfully!";
    } else {
        echo "Error deleting meeting: " . $conn->error;
    }
} else {
    // Handle the case where meeting_id is not provided
    echo "Invalid request.";
}

// Close connection
$conn->close();
?>
