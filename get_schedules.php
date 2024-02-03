<?php
// Include your database connection file or set up the connection here
session_start();
include 'connection.php'; // Replace with your actual database connection file



// Assuming you have $conn as your database connection object

// Perform the query to get schedules
$selectQuery = "SELECT s.*, m.room_name, f.floor_name, sec.sector_name, adm.admin_name, us.user_name
                  FROM schedules s
                  JOIN meeting_rooms m ON s.room_id = m.room_id
				  JOIN floor f ON m.floor_id = f.floor_id
                  JOIN sectors sec ON s.sector_id = sec.sector_id
                  JOIN administrations adm ON s.admin_id = adm.admin_id
				  JOIN users us ON s.user_id = us.user_id where seen_by_security=0 or seen_by_hr=0 order by start_date desc";

// Execute the query
$result = $conn->query($selectQuery);

// Check if the query was successful
if ($result === FALSE) {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
    exit;
}

// Fetch the results into an associative array
$schedules = $result->fetch_all(MYSQLI_ASSOC);

// Loop through schedules and generate HTML for table rows
foreach ($schedules as $schedule) {
    $securityNotification = $schedule['seen_by_security'] ? "Seen by Security" : "Not Seen by Security";
    $hrNotification = $schedule['seen_by_hr'] ? "Seen by HR" : "Not Seen by HR";

    echo "<tr>";
    echo "<td>{$schedule['meeting_title']}</td>";
	 echo "<td>{$schedule['floor_name']}</td>";
	 echo "<td>{$schedule['room_name']}</td>";
	 echo "<td>{$schedule['start_date']}</td>";
	  echo "<td>{$schedule['start_time']}</td>";
	  echo "<td>{$schedule['end_time']}</td>";
    echo "<td>{$securityNotification}</td>";
    echo "<td>{$hrNotification}</td>";
    echo "<td>";
    echo "<button class='btn btn-primary btn-sm' onclick='markAsSeen({$schedule['schedule_id']}, \"security\")'>موافقة الامن</button> ";
    echo "<button class='btn btn-success btn-sm' onclick='markAsSeen({$schedule['schedule_id']}, \"hr\")'>موافقة الشئون االادارية</button>";
    echo "</td>";
    echo "</tr>";
}

if (!isset($_SESSION['alert_shown'])) {
$count_Schedules="SELECT * FROM schedules  where seen_by_security=0 or seen_by_hr=0";				  
//$count_sch=$conn-> query($count_Schedules);
$result1= $conn->query($count_Schedules);
//$row = $result1->fetch_all(MYSQLI_ASSOC);
//$count_sch= (int) $row;
$unapprovedSchedules = $result1->fetch_all(MYSQLI_ASSOC);

// Check if there are not approved schedules
$numUnapproved = count($unapprovedSchedules);


    // Send an alert message with the number of not approved schedules

 if ($numUnapproved > 0) {
	  $timestamp = time();
        // Send an alert message with the number of not approved schedules
      echo "<script>
                if (!sessionStorage.getItem('alertShown')) {
					alert('يوجد عدد $numUnapproved اجتماعات لم يتم الموافقة عليها');
                    //alert('There are $numUnapproved not approved schedules.');
                    sessionStorage.setItem('alertShown', 'true');
                }
              </script>";
			  
			 // echo "<script> alert('There are $numUnapproved not approved schedules.') </script>";

			  
}}
		 //echo "<script> alert('There are $numUnapproved not approved schedules.') </script>";


// Close the database connection
$conn->close();
?>
