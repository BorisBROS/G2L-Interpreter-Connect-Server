<?php 
require_once('config.php');

//To disable caching:
session_cache_limiter('nocache');

$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
/**
 * This is used with the regular expression for parsing rec_type to produce a list of days.
 */
function int_to_day($int){
	global $days;
	return $days[$int];
}

try {
	$db = new PDO("mysql:host=$mysql_server;dbname=$mysql_db", $mysql_user, $mysql_pass);

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	$interpreter_id_exists = array_key_exists('interpreter_id', $_REQUEST);
	if(!$interpreter_id_exists){
		if(array_key_exists('phone_number', $_REQUEST)){
			//Use phone number to look up interpreter id.
			$escaped_phone_number = $db->quote($_REQUEST['phone_number']);
			$find_interpreter_id_sql = "SELECT `id` FROM interpreters WHERE `g2lphone` = $escaped_phone_number";
			$sth = $db->query($find_interpreter_id_sql);
			//Check if the phonenumber is not found and let the user know if that is the case.
			if($sth->rowCount() == 0){
				throw new Exception("Could not id interpreter with phonenumber: $escaped_phone_number");
			}
			$result = $sth->fetch();
			$interpreter_id = $result[0];
			$interpreter_id_exists = true;
		}
	}
	else{
		$interpreter_id = htmlentities($_REQUEST['interpreter_id']);
	}
	
	$event_exists = array_key_exists('event_id', $_REQUEST);
	
	//If we are recieving a submission.
	if(array_key_exists('submit', $_REQUEST)){

		if(strcmp($_REQUEST['submit'], 'save') == 0){
			
			//Generate rec_type field:
			$day_int_set = array();
			for($i = 0; $i < sizeof($days); ++$i){
				if(array_key_exists($days[$i], $_REQUEST)){
					array_push($day_int_set, $i);
				}
			}
			$rec_type = 'week_1___'.implode(',', $day_int_set).'#no';
			
			$start_date_obj = new DateTime($_REQUEST['start_time']);
			$end_date_obj = new DateTime($_REQUEST['end_time']);
			$start_date = $start_date_obj->format('Y-m-d H:i:s');
			
			$event_length = strtotime($_REQUEST['end_time']) - strtotime($_REQUEST['start_time']);
			if($event_length < 0){
				$event_length += 60*60*24;
			}
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
			//error_log($sql);
			$result = $db->query($sql);
		}
	}
	
	include('mobile_scheduler_schedule.php');
	
	$db = null; // close the database connection
}
catch(PDOException $e) {
	$error_message = 'PDOException:'.$e->getMessage().'\n';
	include('mobile_scheduler_error.php');
}
catch(Exception $e) {
	$error_message = 'Exception:'.$e->getMessage().'\n';
	include('mobile_scheduler_error.php');
}
?>