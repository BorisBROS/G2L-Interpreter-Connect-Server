<?php 
require_once('config.php');
require_once('g2l_shared_code.php');

//To disable caching:
session_cache_limiter('nocache');

try {
	$db = new PDO("mysql:host=$mysql_server;dbname=$mysql_db", $mysql_user, $mysql_pass);

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	$interpreter_id_exists = array_key_exists('interpreter_id', $_REQUEST);
	if(!($interpreter_id_exists)){
		if(array_key_exists('phone_number', $_REQUEST)){
			//Use phone number to look up interpreter id.
			$escaped_phone_number = $db->quote($phone_number);
			$find_interpreter_id_sql = "SELECT `id` FROM interpreters WHERE `g2lphone` = $escaped_phone_number";
			$sth = $db->query($find_interpreter_id_sql);
			$result = $sth->fetch();
			error_log($result);
			$interpreter_id = $result['id'];
			$interpreter_id_exists = true;
		}
	}
	else{
		$interpreter_id = $_REQUEST['interpreter_id'];
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
			//error_log("rec_type: $rec_type");
			
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
			error_log($sql);
			$result = $db->query($sql);
		}
	}
?>
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>jQueryMobile - DateBox Demos</title>
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.css" />
	<link type="text/css" href="http://dev.jtsage.com/cdn/datebox/latest/jquery.mobile.datebox.min.css" rel="stylesheet" /> 
	<link type="text/css" href="http://dev.jtsage.com/jQM-DateBox/css/demos.css" rel="stylesheet" /> 
	
	<!-- NOTE: Script load order is significant! -->
    
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.4.min.js"></script> 
	<script type="text/javascript" src="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.js"></script>
	<script type="text/javascript" src="http://dev.jtsage.com/jquery.mousewheel.min.js"></script>
	<script type="text/javascript" src="http://dev.jtsage.com/cdn/datebox/latest/jquery.mobile.datebox.min.js"></script>
	<script type="text/javascript" src="http://dev.jtsage.com/gpretty/prettify.js"></script>
	<link type="text/css" href="http://dev.jtsage.com/gpretty/prettify.css" rel="stylesheet" />
	<script type="text/javascript">
		$('div').live('pagecreate', function() {
			prettyPrint()
		});
	</script>
	
</head>
<body> 

<div data-role="page" class="type-interior">

    <div data-role="header"> 
		<h1>Interpreter Availability Scheduler</h1>
	</div>
	
	<form action="mobile_scheduler_edit.php" method="post" class="ui-body ui-body-a">
		<fieldset>
        	<?php 
        		 if($interpreter_id_exists){
        			echo("<input type='hidden' name='interpreter_id' value='$interpreter_id' />");
        		}
        	?>
			<button type="submit" data-theme="b" name="submit" value="add">Add Time</button>
		</fieldset>
	</form>
	
	<div data-role="content">
		<ul data-role="listview" data-inset="true">
<?php 
	//TODO: Would it be better to do this using javascript and the recurring event connector?
	//		My intuitions is that js would have some advantages, but it would require rewriting this and
	//		I would be dependent on the dhtmlx connector code.
	
	$sql = "SELECT * FROM events_rec";
	if($interpreter_id_exists){
		$sql .= " WHERE `interpreter_id`=$interpreter_id";
	}
	
	$result = $db->query($sql);
	foreach ($result as $row) {
		 
		$event_id = $row['event_id'];
		$start_date = new DateTime($row['start_date']);
		$event_start = $start_date->format('h:i A');
		
		$event_length = new DateInterval('PT'.$row['event_length'].'S');
		
		$end_date = new DateTime($row['start_date']);
		$end_date->add($event_length);
		
		$event_end = $end_date->format('h:i A');
		if($row['rec_type']){
			$matches = array();
			preg_match("|.*_.?_.?_.?_(0?),?(1?),?(2?),?(3?),?(4?),?(5?),?(6?)#.*|", $row['rec_type'], $matches);
			
			// $matches[0] is the complete match so we throw that out (we only want the parenthised subpatterns)
			array_shift($matches);
			// remove empty strings (where digits were not found)
			$matches = array_filter($matches);

			$event_days = array_map("int_to_day", $matches);
			
			$event_day_string = implode(', ', $event_days);
		}
		else{
			$event_day_string = $start_date->format('m/d/Y');
		}
		
		$li_content = "<h3>$event_start - $event_end</h3><p><strong>$event_day_string</strong></p>";
		
		if(! ($interpreter_id_exists)){ //Show read-only list
			$editor_link = "mobile_scheduler_edit.php?event_id=$event_id&interpreter_id=$interpreter_id".
							'&'.implode('=1&', $event_days).'=1'.
							"&start_time=$event_start&end_time=$event_end";
			$li_content = "<a href='$editor_link'>$li_content</a>";
		}

		echo("<li>$li_content</li>");
    }
$db = null; // close the database connection
}
catch(PDOException $e) {
    echo $e->getMessage();
}
?>
		</ul>

	</div><!-- /content -->

</div><!-- /page -->

</body>
</html>