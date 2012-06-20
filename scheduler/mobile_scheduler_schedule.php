<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>jQueryMobile - DateBox Demos</title>
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.css" />
	<link type="text/css" href="http://dev.jtsage.com/cdn/datebox/1.0.0/jquery.mobile.datebox-1.0.0.min.css" rel="stylesheet" /> 
	<link type="text/css" href="http://dev.jtsage.com/jQM-DateBox/css/demos.css" rel="stylesheet" /> 
	
	<!-- NOTE: Script load order is significant! -->
    
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.4.min.js"></script> 
	<script type="text/javascript" src="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.js"></script>
	<script type="text/javascript" src="http://dev.jtsage.com/jquery.mousewheel.min.js"></script>
	<script type="text/javascript" src="http://dev.jtsage.com/cdn/datebox/1.0.0/jquery.mobile.datebox.datebox-1.0.0.min.js"></script>
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
	
	<form action="mobile_scheduler_edit.php" method="get" class="ui-body ui-body-a">
		<fieldset>
        	<?php 
        		//I'm worried about caching issues from switching to a get request...
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
	//Here we build a link of the interpreter's availability times:

	//TODO: Would it be better to do this using javascript and the recurring event connector?
	//		My intuitions is that js would have some advantages, but it would require rewriting this and
	//		I would be dependent on the dhtmlx connector code.
	
	$sql = "SELECT * FROM events_rec";
	if($interpreter_id_exists){
		$sql .= " WHERE `interpreter_id`=$interpreter_id";
	}
	
	$result = $db->query($sql);
	
	if($result->rowCount() == 0){
		echo("<li>No availabilities found.</li>");
	}
	
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
		
		if($interpreter_id_exists){
			//Show list with edit links:
			$editor_link = "mobile_scheduler_edit.php?event_id=$event_id&interpreter_id=$interpreter_id".
							'&'.implode('=1&', $event_days).'=1'.
							"&start_time=$event_start&end_time=$event_end";
			$li_content = "<a href='$editor_link'>$li_content</a>";
		}

		echo("<li>$li_content</li>");
    }
?>
		</ul>

	</div><!-- /content -->

</div><!-- /page -->

</body>
</html>