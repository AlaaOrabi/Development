<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Room Reservations</h1>

    <form method="get">
        <div class="mb-3">
            <label for="specificDate" class="form-label">Choose a specific date:</label>
            <input type="date" id="specificDate" name="specificDate" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Show Reservations</button>
    </form>

    <?php
    // Include your database connection file or set up the connection here
    include 'connection.php'; // Replace with your actual database connection file

    // Assuming you have $conn as your database connection object

    // Check if a specific date is selected
    if (isset($_GET['specificDate'])) {
        $specificDate = $_GET['specificDate'];

        // Get all floors and rooms
        $floorQuery = "SELECT DISTINCT floor_id, floor_name FROM floor";
        $floorResult = $conn->query($floorQuery);

        if ($floorResult !== FALSE) {
            while ($floor = $floorResult->fetch_assoc()) {
                $floorId = $floor['floor_id'];
                $floorName = $floor['floor_name'];

                echo "<div class='mb-4'>";
                echo "<h2>Floor $floorId - $floorName</h2>";

                // Get distinct rooms for the current floor
                $roomQuery = "SELECT DISTINCT room_id, room_name, capacity FROM meeting_rooms WHERE floor_id = $floorId";
                $roomResult = $conn->query($roomQuery);

                if ($roomResult !== FALSE) {
                    while ($room = $roomResult->fetch_assoc()) {
                        $roomId = $room['room_id'];
                        $roomName = $room['room_name'];
                        $capacity = $room['capacity'];

                        echo "<div class='ms-4'>";
                        echo "<h3>Room: $roomName (Room ID: $roomId)</h3>";
                        echo '<span class="badge bg-primary">Room Name: ' . $roomName . '</span>';
                        echo '<span class="badge bg-secondary">Capacity: ' . $capacity . '</span>';

                        // Get reservations for the current room on the specific date
                        $reservationQuery = "SELECT * FROM schedules WHERE room_id = $roomId AND start_date = '$specificDate' ORDER BY start_time ASC";
                        $reservationResult = $conn->query($reservationQuery);

                        if ($reservationResult !== FALSE) {
                            while ($reservation = $reservationResult->fetch_assoc()) {
                                $startTime = $reservation['start_time'];
                                $endTime = $reservation['end_time'];

                                // Display time as a badge
                                echo '<span class="badge bg-success">' . $startTime . ' - ' . $endTime . '</span>';
                            }
                        } else {
                            echo '<p>Error retrieving reservations: ' . $conn->error . '</p>';
                        }

                        echo "</div>";
                    }
                } else {
                    echo "<p>Error retrieving rooms: " . $conn->error . "</p>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>Error retrieving floors: " . $conn->error . "</p>";
        }
    }
    ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
