<?php
session_cache_limiter('nocache'); 
$mobile_scheduler_url = 'mobile_scheduler.php';
if(array_key_exists('interpreter_id', $_REQUEST)){
	$mobile_scheduler_url .= '?interpreter_id=' . htmlentities($_REQUEST['interpreter_id']);
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

<div data-role="page" data-theme="c" id="droIndex"> 
	<!--   <div data-role="header"> 
		<h1>Add Time</h1>
		<a href="<?php echo $mobile_scheduler_url ?>" data-icon="home" data-iconpos="notext">Home</a>
	</div>-->
	
	<form action="<?php echo $mobile_scheduler_url ?>" method="post">
	
	<div class="ui-body ui-body-b">
	<fieldset class="ui-grid-a">
		<div class="ui-block-a"><button data-theme="c" data-icon="home" onclick="window.location.href='<?php echo $mobile_scheduler_url ?>'">Back</button></div>
		<div class="ui-block-b"><button type="submit" data-theme="a" name="submit" value="delete">Delete</button></div>
	</fieldset>
    </div>
	
	<div data-role="content" data-theme="c">
        
        
        	<?php 
        		 if(array_key_exists('event_id', $_REQUEST)){
        		 	$event_id = htmlentities($_REQUEST['event_id']);
        			echo("<input type='hidden' name='event_id' value='$event_id' />");
        		}
        	?>
            <div data-role="fieldcontain">
                <label for="start_time">I will be available at:</label><input name="start_time" id="start_time" type="date"
                <?php 
                if(array_key_exists('start_time', $_REQUEST)){
                	echo('value="'.htmlentities($_REQUEST['start_time']).'"');
                }
                ?> data-role="datebox" data-options='{"mode": "timebox", "timeFormatOverride": 12}' />
		    </div>
			
            <div data-role="fieldcontain">
                <label for="end_time">Until:</label><input name="end_time" id="end_time" type="date"
                <?php 
                if(array_key_exists('end_time', $_REQUEST)){
                	echo('value="'.htmlentities($_REQUEST['end_time']).'"');
                }
                ?> data-role="datebox" data-options='{"mode": "timebox", "timeFormatOverride": 12}' />
		    </div>

    		<div  data-role="fieldcontain">
			 	<fieldset data-role="controlgroup">
			 		<?php 
			 		$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');
			 		foreach($days as $day){
			 			$checked = '';
			 			if(array_key_exists($day, $_REQUEST)){
							$checked = 'checked="checked"';
			 			}
 						echo(
			 			"<input type='checkbox' name='$day' id='$day' class='custom' $checked />
						<label for='$day'>$day</label>"
			 			);
			 		}
			 		?>
			    </fieldset>
			</div>

	</div>
	
    <div class="ui-body ui-body-b">
    <fieldset class="ui-grid-a">
		 <div class="ui-block"><button type="submit" data-theme="d" name="submit" value="save">Save</button></div>
    </fieldset>
	</div>
	</form>
</div>
</body>
</html>
