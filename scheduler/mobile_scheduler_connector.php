<html>
<head>
<meta http-equiv="Refresh" content="url=mobile_scheduler" />
</head>

<body>
<h1>Redirecting...</h1>
</body>
</html>
<?php

require_once('config.php');

error_log("mobile_scheduler_connector");
foreach($_REQUEST as $key=>$val) { 
	error_log("Pkey: $key value: $val");
}

$parameter_names = array(
'start_date', 
'rec_type',
'end_date',
'event_length',
'interpreter_id',
'language_id' #TODO: Add support for language_id
);

$parameters = array();

foreach($parameter_names as $param_name ) { 
	if(array_key_exists($param_name, $_REQUEST)){
		$parameters[$param_name] = htmlentities($_REQUEST[$param_name]);
	}
	else{
		$parameters[$param_name] = null;
	}
}

extract( $parameters );

echo($start_date);
/*
try {
	$db = new PDO("mysql:host=$mysql_server;dbname=$mysql_db", $mysql_user, $mysql_pass);

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	if($event_id){
		$sql = "UPDATE events_rec 
				SET `start_date` = '$start_date', `rec_type` = '$rec_type', `end_date` = '$end_date', `event_length` = '$event_length'
				WHERE `event_id`='$event_id'";
	}
	else{
		
	}
	$result = $db->query($sql);
    $db = null; // close the database connection
}
catch(PDOException $e) {
    error_log($e->getMessage());
}
*/

?>