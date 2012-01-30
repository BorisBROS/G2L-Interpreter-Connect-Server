<?php 
require_once('config.php');

/**
 * This is used with the regular expression for parsing rec_type to produce a list of days.
 */
function int_to_day($int){
	$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
	return $days[$int];
}

try {
	$db = new PDO("mysql:host=$mysql_server;dbname=$mysql_db", $mysql_user, $mysql_pass);

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	$parameter_names = array(
	'phone_number', 
	'interpreter_id'
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
	
	if(!$phone_number && !$interpreter_id){
		throw new Exception("Could not id interpreter.");
	}
	if(!$interpreter_id && $phone_number){
		//Lookup interpreter_id in db
		$interpreter_id = 2;
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
	
	<form action="mobile_scheduler_edit.php<?php echo('interpreter_id='.$interpreter_id); ?>" method="get" class="ui-body ui-body-a">
		<fieldset>
			<button type="submit" data-theme="b" name="submit" value="add">Add Time</button>
		</fieldset>
	</form>
	
	<div data-role="content">
		<ul data-role="listview" data-inset="true">
<?php 
	$sql = "SELECT * FROM events_rec";
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
			$event_days = preg_match(
			            "|.*_.?_.?_.?_(0?),?(1?),?(2?),?(3?),?(4?),?(5?),?(6?)#.*|", $row['rec_type'], $matches);
			
			// $matches[0] is the complete match so we throw that out (we only want the parenthised subpatterns)
			array_shift($matches);
			// remove empty strings (where digits were not found)
			$matches = array_filter($matches);

			$days = array_map("int_to_day", $matches);
			
			$event_days = implode(', ', $days);
		}
		else{
			$event_days = $start_date->format('m/d/Y');
		}
?>
		<li><a href="mobile_scheduler_edit.php?<?php echo('event_id='.$event_id.'&'.implode('=1&', $days).'=1'); ?>">
			
				<h3><?php echo("$event_start - $event_end"); ?></h3>
				<p><strong><?php echo($event_days); ?></strong></p>
				
		</a></li>
<?php
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