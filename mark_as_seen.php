<?php
// Include your database connection file or set up the connection here
include 'connection.php'; // Replace with your actual database connection file

// Assuming you have $conn as your database connection object

// Perform the update query
$scheduleId = $_POST['schedule_id'];
$seenType = $_POST['seen_type'];

// Update the seen status based on the provided type
switch ($seenType) {
    case 'security':
        $updateQuery = "UPDATE schedules SET seen_by_security = 1 WHERE schedule_id = $scheduleId";
        break;

    case 'hr':
        $updateQuery = "UPDATE schedules SET seen_by_hr = 1 WHERE schedule_id = $scheduleId";
        break;

    // Handle other cases if needed

    default:
        // Invalid seen_type
        echo json_encode(['status' => 'error', 'message' => 'Invalid seen_type']);
        exit;
}

// Execute the update query
if ($conn->query($updateQuery) === 1) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}

// Close the database connection
$conn->close();
?>
