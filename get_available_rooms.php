<?php
// Include your database connection logic here
// Example:

 $conn = new mysqli('localhost', 'root', '', 'room_scheduler');
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
 }

// Get parameters from the AJAX request
$floor_id = $_GET['floor_id'];
$start_date = $_GET['start_date'];
$start_time = $_GET['start_time'];
$end_time = $_GET['end_time'];
$persons = $_GET['persons'];

// Sanitize input data (for illustrative purposes, you may need to improve this)
$floor_id = mysqli_real_escape_string($conn, $floor_id);
$start_date = mysqli_real_escape_string($conn, $start_date);
$start_time = mysqli_real_escape_string($conn, $start_time);
$end_time = mysqli_real_escape_string($conn, $end_time);
$persons = mysqli_real_escape_string($conn, $persons);

// Your SQL query to retrieve available rooms based on the parameters
$roomsql = "SELECT room_id,room_name, capacity FROM meeting_rooms WHERE floor_id = '$floor_id' AND capacity >= '$persons'
        AND room_id NOT IN (
            SELECT room_id FROM schedules
            WHERE 
			start_date = '$start_date'
            AND (
                (start_time >= '$start_time' AND start_time < '$end_time')
                OR (end_time > '$start_time' AND end_time <= '$end_time')
            )
            OR (
                (start_time <= '$start_time' AND end_time >= '$end_time')
            )
        )";

$result = $conn->query($roomsql);

if ($result) {
    // Output data in HTML format
	//echo '<option selected="" disabled="">اختر الغرفة</option>';
    while ($row = $result->fetch_assoc()) {
		
		echo '<option value="'.$row['room_id'].'">'.'رقم الغرفه  '.$row['room_name'].'__السعة '.$row['capacity'].'</option>';
    }
   
} else {
    // Handle the case where the query fails
    echo "Error: " . $roomsql . "<br>" . $conn->error;
}

// Close the database connection (optional, as it may be closed automatically)
 $conn->close();
?>
