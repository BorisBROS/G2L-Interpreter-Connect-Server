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
	<div data-role="header"> 
		<h1>Add Time</h1>
		<a href="mobile_scheduler.php" data-icon="home" data-iconpos="notext">Home</a>
	</div>
	
	<div data-role="content" data-theme="c">
    
        <form action="mobile_scheduler_connector.php" method="post">

            <div data-role="fieldcontain">
                <label for="at-date">I will be available at:</label><input name="at-date" id="at-date" type="date" data-role="datebox" data-options='{"mode": "timebox", "timeFormatOverride": 12}' />
		    </div>
			
            <div data-role="fieldcontain">
                <label for="until-date">Until:</label><input name="until-date" id="until-date" type="date" data-role="datebox" data-options='{"mode": "timebox", "timeFormatOverride": 12}' />
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
					<input type="checkbox" name="checkbox-1a" id="checkbox-1a" class="custom" />
					<label for="checkbox-1a">Monday</label>

					<input type="checkbox" name="checkbox-2a" id="checkbox-2a" class="custom" checked="checked" />
					<label for="checkbox-2a">Tuesday</label>

			    </fieldset>
			</div>
            
        	<div class="ui-body ui-body-b">
    		<fieldset class="ui-grid-a">
				<div class="ui-block-a"><button type="submit" data-theme="d">Delete</button></div>
				<div class="ui-block-b"><button type="submit" data-theme="a">Save</button></div>
    	    </fieldset>
    		</div>
        
		</form>
        
	</div>
</div>
</body>
</html>
