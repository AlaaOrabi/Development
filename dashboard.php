<?php
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

$start_date = $_POST['start_date'] ?? date('Y-m-d');
$end_date = $_POST['end_date'] ?? date('Y-m-d', strtotime('+1 week'));

// Fetch all scheduled meetings with sector and admin names within the specified date range
$meetingsQuery = "SELECT s.*, m.room_name, f.floor_name, sec.sector_name, adm.admin_name, us.user_name
                  FROM schedules s
                  JOIN meeting_rooms m ON s.room_id = m.room_id
				  JOIN floor f ON m.floor_id = f.floor_id
                  JOIN sectors sec ON s.sector_id = sec.sector_id
                  JOIN administrations adm ON s.admin_id = adm.admin_id
				  JOIN users us ON s.user_id = us.user_id
                  WHERE s.start_date BETWEEN '$start_date' AND '$end_date'";

$meetingsResult = $conn->query($meetingsQuery);

// Fetch all meeting rooms
$roomsQuery = "SELECT m.*,f.floor_name   FROM meeting_rooms m JOIN floor f ON m.floor_id = f.floor_id ";
$roomsResult = $conn->query($roomsQuery);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/schedule_dash.css">
</head>
<body>

<div class="container mt-5">
    <h2>لوحة مؤشرات المتحكم</h2>
    <hr>

    <form method="post" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="start_date" class="mr-2">تاريخ البداية:</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
        </div>
        <div class="form-group mr-2">
            <label for="end_date" class="mr-2">تاريخ النهاية:</label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
        </div>
        <button type="submit" class="btn btn-primary">بحث</button>
    </form>

    <h3>اجتماعات المسجلة</h3>

    <table class="table">
        <thead>
        <tr>
            <th>الغرفة</th>
            <th>رقم الاشخاص</th>
            <th>تاريخ البداية</th>
            <th>وقت البداية</th>
            <th>وقت النهاية</th>
            <th>المستخدم</th>
            <th>القطاع</th>
            <th>الادارة</th>
        </tr>
        </thead>
        <tbody>
        <?php
        while ($meeting = $meetingsResult->fetch_assoc()) {
            echo "<tr";
            // Check if the meeting is in progress (you can customize this logic based on your requirements)
            if (strtotime($meeting['start_date'] . ' ' . $meeting['start_time']) <= time() &&
                strtotime($meeting['end_time']) >= time()) {
               // echo " style='background-color: #ff6666;'";
            }
            echo ">";
            echo "<td>{$meeting['room_name']}</td>";
            echo "<td>{$meeting['persons']}</td>";
            echo "<td>{$meeting['start_date']}</td>";
            echo "<td>{$meeting['start_time']}</td>";
            echo "<td>{$meeting['end_time']}</td>";
            echo "<td>{$meeting['user_name']}</td>";
            echo "<td>{$meeting['sector_name']}</td>";
            echo "<td>{$meeting['admin_name']}</td>";
           
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>

    <h3>الغرف المتاحة</h3>

    <div class="row">
        <?php
        while ($room = $roomsResult->fetch_assoc()) {
            echo "<div class='col-md-4 mb-3'>";
            echo "<div class='card";
            // Check if the room is available (you can customize this logic based on your requirements)
            if (!roomIsReserved($room['room_id'])) {
                echo " text-white bg-success'";
            } else {
                echo "'";
            }
            echo ">";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title'>{$room['room_name']}</h5>";
            echo "<p class='card-text'>رقم الدور: {$room['floor_name']}</p>";
            echo "<p class='card-text'>سعة القاعة: {$room['capacity']}</p>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }

        function roomIsReserved($roomId)
        {
            global $meetingsResult;
            while ($meeting = $meetingsResult->fetch_assoc()) {
                if ($meeting['room_id'] == $roomId &&
                    strtotime($meeting['start_date'] . ' ' . $meeting['start_time']) <= time() &&
                    strtotime($meeting['end_time']) >= time()) {
                    return true;
                }
            }
            return false;
        }
        ?>
    </div>
</div>

<!-- Bootstrap JS and jQuery (optional, for some Bootstrap features) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>s