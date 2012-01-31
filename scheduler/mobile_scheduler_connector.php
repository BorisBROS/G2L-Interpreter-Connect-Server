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
		
		$event_length_obj = $start_date_obj->diff($end_date_obj);
		$event_length = $event_length_obj->format('%s');
		error_log("end date:".$end_date_obj->format('Y-m-d H:i:s'));
		error_log("start_date: $start_date");
		error_log("event_length: $event_length");
		
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
<meta http-equiv="Refresh" content="url=http://google.com" />
</head>

<body>
<h1>Redirecting...</h1>
</body>
</html>