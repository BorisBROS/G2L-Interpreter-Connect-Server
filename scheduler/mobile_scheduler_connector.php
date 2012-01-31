<?php

require_once('config.php');
require_once('g2l_shared_code.php');

/*
error_log("mobile_scheduler_connector");
foreach($_REQUEST as $key=>$val) { 
	error_log("Pkey: $key value: $val");
}
*/

$event_id = $_REQUEST['event_id'] or -1;

try {
	$db = new PDO("mysql:host=$mysql_server;dbname=$mysql_db", $mysql_user, $mysql_pass);

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	
	$sql = null;
	
	if(strcmp($_REQUEST['submit'], 'save') == 0){
		
		//Generate rec_type field:
		$day_int_set = array();
		for($i = 0; $i < sizeof($days); ++$i){
			if(array_key_exists($days[$i], $_REQUEST)){
				array_push($day_int_set, $i);
			}
		}
		$rec_type = 'week_1___'.implode(',', $day_int_set).'#no';
		error_log("rec_type: $rec_type");
		
		$start_date_obj = new DateTime($_REQUEST['start_time']);
		$end_date_obj = new DateTime($_REQUEST['end_time']);
		$start_date = $start_date_obj->format('Y-m-d H:i:s');
		
		$event_length = strtotime($_REQUEST['end_time']) - strtotime($_REQUEST['start_time']);
		if($event_length < 0){
			$event_length += 60*60*24;
		}
		/*
		error_log("end date:".$end_date_obj->format('Y-m-d H:i:s'));
		error_log("start_date: $start_date");
		error_log("event_length: $event_length");
		*/
		if($event_id >= 0){
			$sql = "UPDATE events_rec 
					SET `start_date` = '$start_date', `rec_type` = '$rec_type', `event_length` = '$event_length'
					WHERE `event_id`='$event_id'";
			
		}
		else{
			$interpreter_id = $_REQUEST['interpreter_id'];
			$sql = "INSERT INTO events_rec (`start_date`, `end_date`,             `rec_type`, `event_length`, `interpreter_id`, `language_id`)
									VALUES ('$start_date', '9999-02-01 00:00:00', '$rec_type','$event_length', '$interpreter_id', '$interpreter_id')";
		}
	}
	else if(strcmp($_REQUEST['submit'], 'delete') == 0){
		if($event_id >= 0){
			$sql = "DELETE FROM events_rec WHERE `event_id`='$event_id'";
		}
		else{
			//Do Nothing
		}
	}

	if($sql){
		$result = $db->query($sql);
	}
    $db = null; // close the database connection
}
catch(PDOException $e) {
    error_log($e->getMessage());
}


?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="refresh" content="5;url=http://google.com" />
</head>
<body>
<h1>Redirecting...</h1>
</body>
</html>