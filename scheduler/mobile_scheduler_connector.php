<?php

require_once('config.php');
require_once('g2l_shared_code.php');

error_log("mobile_scheduler_connector");
foreach($_REQUEST as $key=>$val) { 
	error_log("Pkey: $key value: $val");
}


try {
	$db = new PDO("mysql:host=$mysql_server;dbname=$mysql_db", $mysql_user, $mysql_pass);

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	
	$sql = null;
	
	if(strcmp($_REQUEST['submit'], 'save') == 0){
		
		//Generate rec_type field:
		$day_bit_set = array();
		foreach($days as $day){
			array_push($day_bit_set, array_key_exists($day, $_REQUEST));
		}
		$rec_type = 'week_1___'.implode(',', $day_bit_set).'#no';
		echo($rec_type);
		
		$start_date_obj = new DateTime($_REQUEST['start_time']);
		$end_date_obj = new DateTime($_REQUEST['end_time']);
		$start_date = $start_date_obj->format('Y-m-d H:i:s');
		$event_length_obj = $end_date_obj->diff($start_date_obj);
		$event_length = $event_length_obj ->format('%s');
		
		echo($event_length);
		
		if(array_key_exists('event_id', $_REQUEST)){
/*
			$sql = "UPDATE events_rec 
					SET `start_date` = '$start_date', `rec_type` = '$rec_type', `event_length` = '$event_length'
					WHERE `event_id`='$event_id'";
		*/
		}
		else{
			//Add query...
		}
	}
	else if(strcmp($_REQUEST['submit'], 'delete') == 0){
		if(array_key_exists('event_id', $_REQUEST)){
			$event_id = $_REQUEST['event_id'];
			$sql = "UPDATE events_rec 
					SET `start_date` = '$start_date', `rec_type` = '$rec_type', `event_length` = '$event_length'
					WHERE `event_id`='$event_id'";
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
<html>
<head>
<meta http-equiv="Refresh" content="url=mobile_scheduler.php?interpreter_id=2" />
</head>

<body>
<h1>Redirecting...</h1>
</body>
</html>