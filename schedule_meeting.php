<?php
session_start(); // Start the session

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login_page.php");
    exit();
}

// Your database connection code here
include('connection.php');
$link = mysqli_connect('localhost','root','','room_scheduler');
// Use user data for scheduling a meeting
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$sector_id = $_SESSION['sector_id'];
$admin_id = $_SESSION['admin_id'];
$sector_name = $_SESSION['sector_name'];
$admin_name = $_SESSION['admin_name'];

//$location=$_GET['location'];
// Fetch meeting rooms, sectors, and administrations for dropdowns
$floorQuery = "SELECT floor_id,floor_name FROM floor";
$floorSql = mysqli_query($link, $floorQuery);
$roomQuery = "SELECT room_id, room_name, location, capacity,floor_id FROM meeting_rooms";
$roomResult = $conn->query($roomQuery);
//$floorQuery = "SELECT floor_id, floor_name FROM floor";
//$floorResult = $conn->query($floorQuery);



$userQuery = "SELECT user_id, user_name FROM users";
$userResult = $conn->query($userQuery);

$sectorQuery = "SELECT sector_id, sector_name FROM sectors";
$sectorResult = $conn->query($sectorQuery);

$adminQuery = "SELECT admin_id, admin_name FROM administrations";
$adminResult = $conn->query($adminQuery);


 function legal_input($value) {
    $value = trim($value);
    $value = stripslashes($value);
    $value = htmlspecialchars($value);
    return $value;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data

	//$location= $_POST["location"];
 //
 

	$meeting_title=$_POST["meeting_title"];
    $persons = $_POST["persons"];
    $start_date = $_POST["start_date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
	$floor_id= $_POST["floor_id"];
	$room_id =$_POST["room_id"];
   //$user_name = $_POST["user_name"];
    //$sector_id = $_POST["sector_id"];
   // $admin_id = $_POST["admin_id"];
	//$user_id=$_POST["user_id"];
    // Validate input (you can add more validation as needed)
    //if (empty($room_id) || empty($persons) || empty($start_date) || empty($start_time) || empty($end_time) || empty($user_name) || empty($sector_id) || empty($admin_id)) {
         // Fetch room capacity
	//$roomName="SELECT room_id, room_name, location, capacity FROM meeting_rooms WHERE location like '4th%'";
    //$roomNameResult = $conn->query($roomName);
	 $current_date = date("Y-m-d h:i:s");
	 if( $start_date < $current_date){
		   echo "<script>alert('Error: please enter correct date.');</script>";
        exit; 
	 }
			
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $room_id = $_POST["room_id"];
	$floor_id= $_POST["floor_id"];
	
	$meeting_title=$_POST["meeting_title"];
    $persons = $_POST["persons"];
    $start_date = $_POST["start_date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
	$new_end_time = date('H:i:s', strtotime($end_time) + 3600);
   //$user_name = $_POST["user_name"];
    //$sector_id = $_POST["sector_id"];
   // $admin_id = $_POST["admin_id"];
	//$user_id=$_POST["user_id"];
    // Validate input (you can add more validation as needed)
    //if (empty($room_id) || empty($persons) || empty($start_date) || empty($start_time) || empty($end_time) || empty($user_name) || empty($sector_id) || empty($admin_id)) {
         // Fetch room capacity
	//$roomName="SELECT room_id, room_name, location, capacity FROM meeting_rooms WHERE location like '4th%'";
    //$roomNameResult = $conn->query($roomName);
	
    $capacityQuery = "SELECT capacity FROM meeting_rooms WHERE room_id = '$room_id'";
    $capacityResult = $conn->query($capacityQuery);
	
    if ($capacityResult->num_rows > 0) {
        $row = $capacityResult->fetch_assoc();
        $room_capacity = (int)$row["capacity"]; // Convert to integer
    } else {
        // Handle the case where room capacity is not found
        echo "<script>alert('Error: Room capacity not found for the selected room.');</script>";
        exit; // Terminate the script
    }

    // Validate input (you can add more validation as needed)
    if (empty($room_id) || empty($persons) || empty($start_date) || empty($start_time) || empty($end_time) || empty($user_id) || empty($sector_id) || empty($admin_id)) {
        echo "<script>alert('Please fill out all fields.');</script>";
    } else {
        // Check if the number of persons is suitable for the room capacity
        $selected_capacity = (int)$_POST["persons"]; // Convert to integer

        if ($selected_capacity < 2 || $selected_capacity >= $room_capacity) {
            echo "<script>alert('Number of persons must be at least 2 and should not exceed the room capacity.');</script>";
        } else{
            $conflictQuery = "SELECT * FROM schedules
                              WHERE room_id = '$room_id'
                                AND start_date = '$start_date'
                                AND (
                                    (start_time <= '$start_time' AND end_time >= '$start_time') OR
                                    (start_time <= '$end_time' AND end_time >= '$end_time') OR
                                    (start_time >= '$start_time' AND end_time <= '$end_time')
                                )";

            $conflictResult = $conn->query($conflictQuery);
		
            if ($conflictResult->num_rows > 0) {
                echo "<script>alert('The selected room is not available during the specified time range.');</script>";
            } else {
                // Insert data into the database
                $sql = "INSERT INTO schedules (room_id,meeting_title, persons, start_date, start_time, end_time, user_id, sector_id, admin_id) 
                        VALUES ('$room_id', '$meeting_title','$persons', '$start_date', '$start_time', '$new_end_time', '$user_id', '$sector_id', '$admin_id')";
                
                if ($conn->query($sql) === TRUE) {
            // echo "<script>alert('Meeting scheduled successfully! Room ID: $room_id, meeting_title: $meeting_title,Persons: $persons, Start Date: $start_date, Start Time: $start_time, End Time: $end_time, User: $user_id, Sector: $sector_id, Admin: $admin_id');</script>";
			 // echo '<div class="alert alert-danger" role="alert">Meeting scheduled successfully! Room ID:'$room_id', meeting_title: '$meeting_title',Persons: '$persons', Start Date: '$start_date', Start Time: '$start_time', End Time: '$end_time', User: '$user_id', Sector: '$sector_id', Admin: '$admin_id'". 'h</b>.</div>';
              echo '<div class="alert alert-danger" role="alert">تم حجز القاعة بنجاح في<b>'.$_POST["room_id"].'</b> لعدد <b>' . $_POST["persons"] . ' أشخاص</b> في التوقيت <b>' . $_POST["start_time"] . 'h - ' . $_POST["end_time"] . 'h</b>.</div>';
			
			
    } else
                    echo "<script>alert('Error: " . $conn->error . "');</script>";
                }
            }
        }
    
 }
 }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <title>Schedule Meeting</title>
    <!-- Bootstrap CSS link -->
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/schedule_meet.css"/>
	
	
	
</head>
<body class="bg-light">
<!-- Logout button -->
<div class="text-right mb-3">
 <a href="logout_page.php" class="btn btn-danger">خروج</a>
 <a href="user_dashboard.php" class="btn btn-dang">لوحة مؤشرات المستخدم </a>
</div>
<!--<div class="text-left mb-3">
 <a href="user_dashboard.php" class="btn btn-danger">لوحة مؤشرات المستخدم </a>
</div>-->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h2 class="text-center">حجز قاعة</h2>
                </div>
                <div class="card-body">
                    <form method="post" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>>
                        <div class="form-group">
                            <label for="meeting_title">عنوان الاجتماع:</label>
                            <input type="text" id="meeting_title" name="meeting_title" class="form-control" required>
                        </div>
						
				<div class="form-group">
                        <label for="floor_id" >رقم الدور:</label>
						<select class="custom-select select2" id="floor_id" name="floor_id"   onchange="showRoom()">
						<option value="" >اختر الدور</option>
                        
			      <?php while($floorRow = mysqli_fetch_assoc($floorSql) ){ ?>
			      <option value="<?php echo $floorRow['floor_id'];?>"><?php echo $floorRow['floor_name'];?></option>
			  	  <?php } ?>
					  </select>
					 </div>		

                        <div class="form-group">
                            <label for="persons">عدد الاشخاص:</label>
                            <input type="number" id="persons" name="persons" class="form-control"  required onchange="showRoom()">
                        </div>
                        <div class="form-group">
                            <label for="start_date">تاريخ الاجتماع:</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" required onchange="showRoom()">
                        </div>
                        <div class="form-group">
                            <label for="start_time"> بداية الاجتماع:</label>
                            <input type="time" id="start_time" name="start_time" class="form-control"  required onchange="showRoom()">
                        </div>
                        <div class="form-group">
                            <label for="end_time"> نهاية الاجتماع:</label>
                            <input type="time" id="end_time" name="end_time" class="form-control"   required onchange="showRoom()">
                       



					 <div class="form-group">
                        <label for="room_id">غرفة الاجتماع: في حالة عدم ظهور رقم الغرفة يعني  ان كل غرف الدور محجوزة اختر دور اخر</label>
						<select class="custom-select select2" name="room_id" id="room_id" required >
						<option     value="" >رقم الغرفة</option>
                   
					 </select>
					 </div>
                        <!-- Automatically filled user data -->
                        <div class="form-group">
                            <label for="user_name">اسم المستخدم:</label>
                            <input type="text" id="user_name" name="user_name" value="<?php echo $user_name; ?>" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="sector_name">اسم القطاع:</label>
                            <input type="text" id="sector_name" name="sector_name" value="<?php echo $sector_name; ?>" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="admin_name">اسم الإدارة:</label>
                            <input type="text" id="admin_name" name="admin_name" value="<?php echo $admin_name; ?>" class="form-control" readonly>
                        </div>
						
                 
                        <button type="submit" class="btn btn-success btn-block">حجز قاعة</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
	/*	$(document).ready(function(){
			$("#floor_id").change(function(){
				var room_id = $("#floor_id").val();
				$.ajax({
					url: 'data.php',
					method: 'post',
					data: 'room_id=' + room_id
				}).done(function(room_id){
					console.log(room_id);
					room_id = JSON.parse(room_id );
					$('#room_id').empty();
					room_id .forEach(function($room){
						$('#room_id').append('<option>' + room_name + '</option>')
					})
				})
			})
		})*/
	/*	function showRoom(str) {
	    if (str == "") {
	        document.getElementById("room_id").innerHTML = "";
	        return;
	    } else {
	        if (window.XMLHttpRequest) {
	            // code for IE7+, Firefox, Chrome, Opera, Safari
	            xmlhttp = new XMLHttpRequest();
	        } else {
	            // code for IE6, IE5
	            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	        }
	        xmlhttp.onreadystatechange = function() {
	            if (this.readyState == 4 && this.status == 200) {
	                document.getElementById("room_id").innerHTML = this.responseText;
	            }
	        };
	        xmlhttp.open("GET","data.php?fid="+str,true);
	        xmlhttp.send();
	    }
	}*/
	/*function updateAvailableRooms() {
    // Get values from the form
	 var persons = $("#persons").val();
    var start_date = $("#start_date").val();
    var start_time = $("#start_time").val();
    var end_time = $("#end_time").val();
   

    // Perform an AJAX request to fetch available rooms
    $.ajax({
        type: "POST",
        url: "get_available_rooms.php", // Replace with the actual PHP script
        data: {
			persons: persons,
            start_date: start_date,
            start_time: start_time,
            end_time: end_time
            
        },
        success: function(response) {
            // Parse the JSON response
            var availableRooms = JSON.parse(response);

            // Clear existing options
           $("#room_id").empty();

            // Populate available rooms and capacities dropdown
            for (var i = 0; i < availableRooms.length; i++) {
                var optionText = availableRooms[i].room_id + " (Name: " + availableRooms[i].room_name + ")";
                $("#room_id").append("<option value='" + availableRooms[i].room_id + "'>" + optionText + "</option>");
            }
        },
        error: function() {
            alert("Error fetching available rooms.");
        }
    });
}*/
 
    function showRoom() {
            var floorDropdown = document.getElementById("floor_id");
            var floor_id = floorDropdown.value;

            var start_date = document.getElementById("start_date").value;
            var start_time = document.getElementById("start_time").value;
            var end_time = document.getElementById("end_time").value;
            var persons = document.getElementById("persons").value;

            if (floor_id == "" || start_date == "" || start_time == "" || end_time == "" || persons == "") {
                document.getElementById("room_id").innerHTML = "<option value=''>احجز غرفة</option>";
                return;
            }
			

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("room_id").innerHTML = this.responseText;
                }
            };

            var url = "get_available_rooms.php?" +
                      "floor_id=" + encodeURIComponent(floor_id) +
                      "&start_date=" + encodeURIComponent(start_date) +
                      "&start_time=" + encodeURIComponent(start_time) +
                      "&end_time=" + encodeURIComponent(end_time) +
                      "&persons=" + encodeURIComponent(persons);

            xhr.open("GET", url, true);
            xhr.send();
        }
    </script>



	</script>
<!-- Bootstrap JS and jQuery scripts (place them at the end of the body for better performance) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
