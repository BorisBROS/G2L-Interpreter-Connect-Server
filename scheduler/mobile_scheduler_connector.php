<?php

require_once('config.php');
require_once('g2l_shared_code.php');
session_cache_limiter('nocache');
/*
error_log("mobile_scheduler_connector");
foreach($_REQUEST as $key=>$val) { 
	error_log("Pkey: $key value: $val");
}
*/

$event_exists = array_key_exists('event_id', $_REQUEST);

if(!array_key_exists('interpreter_id', $_REQUEST)){
	echo("<pre>No interpreter id supplied</pre>");
	die();
}
$interpreter_id = $_REQUEST['interpreter_id'];

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
		//error_log("rec_type: $rec_type");
		
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
		if($event_exists){
			$event_id = $db->quote($_REQUEST['event_id']);
			$sql = "UPDATE events_rec 
					SET `start_date` = '$start_date', `rec_type` = '$rec_type', `event_length` = $event_length
					WHERE `event_id`=$event_id";
			
		}
		else{
			$escaped_interpreter_id = $db->quote($interpreter_id);
			$sql = "INSERT INTO events_rec (`start_date`, `end_date`,             `rec_type`, `event_length`, `interpreter_id`,            `language_id`)
									VALUES ('$start_date', '9999-02-01 00:00:00', '$rec_type','$event_length', $escaped_interpreter_id, $escaped_interpreter_id)";
		}
	}
	else if(strcmp($_REQUEST['submit'], 'delete') == 0){
		if($event_exists){
			$event_id = $db->quote($_REQUEST['event_id']);
			$sql = "DELETE FROM events_rec WHERE `event_id`=$event_id";
		}
		else{
			//Do Nothing
		}
	}

	if($sql){
		error_log($sql);
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
<meta http-equiv="refresh" content="0;url=mobile_scheduler.php" />
</head>
<body>
<h1>Redirecting...</h1>
</body>
</html>