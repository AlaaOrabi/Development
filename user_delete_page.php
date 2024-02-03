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

    // Delete the meeting
    $deleteMeetingQuery = "DELETE FROM schedules WHERE schedule_id = $schedule_id AND user_id = {$_SESSION['user_id']}";
    
    if ($conn->query($deleteMeetingQuery) === TRUE) {
        // Meeting deleted successfully, redirect to the user dashboard
        header("Location: user_dashboard.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    // Redirect to the user dashboard if the meeting_id parameter is not set
    header("Location: user_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Meeting</title>
    <!-- Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h2 class="text-center">Delete Meeting</h2>
                </div>
                <div class="card-body">
                    <p class="text-danger">Are you sure you want to delete this meeting?</p>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?schedule_id=$schedule_id"); ?>">
                        <input type="hidden" name="schedule_id" value="<?php echo $schedule_id; ?>">
                        <button type="submit" class="btn btn-danger btn-block">Yes, Delete Meeting</button>
                    </form>
                    <a href="user_dashboard.php" class="btn btn-secondary btn-block mt-3">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and jQuery scripts (place them at the end of the body for better performance) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
