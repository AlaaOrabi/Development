<?php
session_start(); // Start the session

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login_page.php");
    exit();
}

// Your database connection code here
include_once("connection.php"); // Assuming you have a separate file for database connection

// Use user data for displaying the dashboard
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch scheduled meetings for the user
$scheduledMeetingsQuery = " SELECT s.*, m.room_name, f.floor_name, sec.sector_name, adm.admin_name, us.user_name
                  FROM schedules s
                  JOIN meeting_rooms m ON s.room_id = m.room_id
				  JOIN floor f ON m.floor_id = f.floor_id
                  JOIN sectors sec ON s.sector_id = sec.sector_id
                  JOIN administrations adm ON s.admin_id = adm.admin_id
				  JOIN users us ON s.user_id = us.user_id WHERE s.user_id = $user_id order by start_date desc" ;
$scheduledMeetingsResult = $conn->query($scheduledMeetingsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Bootstrap CSS link -->
     <link rel="stylesheet" href="css/dash.css"/>
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>

</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center">مرحبا بك, <?php echo $user_name; ?></h2>
                </div>
                <div class="card-body">
                    <a href="schedule_meeting.php" class="btn btn-success mb-3">لحجز قاعة جديدة</a>
					<a href="logout_page.php" class="btn btn-success mb-3">الخروج</a>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>عنوان الأجتماع</th>
                                <th>رقم الغرفة</th>
								<th>الدور</th>
                                <th>عدد الحاضرين</th>
                                <th>تاريخ الأجتماع </th>
                                <th>بداية الأجتماع </th>
                                <th>نهاية الأجتماع </th>
                                <th>تعديل/حذف</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($scheduledMeetings = $scheduledMeetingsResult->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$scheduledMeetings['meeting_title']}</td>";
                                echo "<td>{$scheduledMeetings['room_name']}</td>";
								echo "<td>{$scheduledMeetings['floor_name']}</td>";
                                echo "<td>{$scheduledMeetings['persons']}</td>";
                                echo "<td>{$scheduledMeetings['start_date']}</td>";
                                echo "<td>{$scheduledMeetings['start_time']}</td>";
								$endTime = date('H:i:s', strtotime($scheduledMeetings['end_time']) - 3600);
                                echo "<td>{$endTime}</td>";
                                echo "<td>
                                        <a href='user_edit_page.php?schedule_id={$scheduledMeetings['schedule_id']}' class='btn btn-warning btn-sm'>تعديل</a>
                                        <a href='user_delete_page.php?schedule_id={$scheduledMeetings['schedule_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this meeting?\");'>حذف</a>
                                      </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
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
