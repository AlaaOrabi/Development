<?php 
$link = mysqli_connect('localhost','root','','room_scheduler');
if (!$link) {
    die('Could not connect: ' . mysqli_error($con));
}

//show city names by country id
if(isset($_REQUEST['fid'])){
	
	$roomQuery = "SELECT * FROM meeting_rooms WHERE floor_id = ".$_REQUEST['fid'];
	$roomSql = mysqli_query($link, $roomQuery);
	echo '<option selected="" disabled="">اختر الغرفة</option>';
	while( $roomRow = mysqli_fetch_assoc($roomSql) ){
		echo '<option value="'.$roomRow['room_id'].'">'.'رقم الغرفه  '.$roomRow['room_name'].'__السعة '.$roomRow['capacity'].'</option>';
	}
}


/*if(isset($_REQUEST['start_date','start_time','end_time','persons','fid'])){
	
	$persons = $_POST["persons"];
    $start_date = $_POST["start_date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
	$roomQuery="SELECT m.* FROM schedules s
		JOIN meeting_rooms m ON s.room_id = m.room_id
                               WHERE start_date = '$start_date'
                                AND (
                                    (start_time <=" .$_REQUEST['start_time'] "AND end_time >= ".$_REQUEST['start_time']") OR
                                    (start_time <= ".$_REQUEST['end_time'] "AND end_time >= "$_REQUEST['end_time']") OR
                                    (start_time >= ".$_REQUEST['start_time']" AND end_time <= "$_REQUEST['end_time']"))
 				AND s.persons <" .$_REQUEST['persons']" )
                                AND floor_id = ".$_REQUEST['fid'];
	
	$roomQuery = "SELECT * FROM meeting_rooms WHERE floor_id = ".$_REQUEST['fid'];
	$roomSql = mysqli_query($link, $roomQuery);
	echo '<option selected="" disabled="">اختر الغرفة</option>';
	while( $roomRow = mysqli_fetch_assoc($roomSql) ){
		echo '<option value="'.$roomRow['room_id'].'">'.'رقم الغرفه  '.$roomRow['room_name'].'__السعة '.$roomRow['capacity'].'</option>';
	}
}*/
 ?>

