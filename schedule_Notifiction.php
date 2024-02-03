<!-- schedule_view.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule View</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<link rel="stylesheet" href="css/schedule_Nof.css"/>
</head>
<body>

    <div class="container mt-5">
        <h1 class="mb-4">لوحة الموافقة على الاجتماعات</h1>

        <table class="table">
            <thead>
                <tr>
                    <th>عنوان الاجتماع</th>
					<th>اسم الدور</th>
					<th>رقم الغرفة</th>
					<th>تاريخ البداية</th>
					<th>وقت البداية</th>
					<th>وقت الانتهاء</th>
                    <th>موافقة الامن</th>
                    <th>موافقة الشئون الادارية</th>
                    <th>تنشيط الموافقة</th>
                </tr>
            </thead>
            <tbody id="scheduleList">
                <!-- Schedules will be displayed here -->
            </tbody>
        </table>
    </div>

    <script>
        // Function to fetch and display schedules
        function fetchSchedules() {
            $.ajax({
                url: 'get_schedules.php',
                method: 'GET',
                success: function(response) {
                    $('#scheduleList').html(response);
                }
            });
        }

        // Fetch schedules initially
        fetchSchedules();

        // Function to mark a schedule as seen
        function markAsSeen(scheduleId, seenType) {
            $.ajax({
                url: 'mark_as_seen.php',
                method: 'POST',
                data: { schedule_id: scheduleId, seen_type: seenType },
                success: function(response) {
                    console.log(response);
                    // Update the schedule list after marking as seen
                    fetchSchedules();
                }
            });
        }

        // Set up a timer to refresh the schedule list every 5 seconds
        setInterval(fetchSchedules, 5000);
    </script>

</body>
</html>
