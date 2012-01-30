<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
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
		<h1>jQueryMobile - DateBox</h1>
		<a href="../" data-icon="home" data-iconpos="notext">Home</a>
	</div>

	<div data-role="content">
		<ul data-role="listview" data-inset="true">
<?php 
require_once('config.php');

try {
    $db = new PDO("mysql:host=$mysql_server;dbname=$mysql_db", $mysql_user, $mysql_pass);
 
    echo "Connected to database"; // check for connection
 
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
 
    $sql = "SELECT * FROM events_rec";
    $result = $db->query($sql);
    foreach ($result as $row) {
    	
    	$event_id = $result['event_id'];
    	$event_start = $result['start_date'];
    	$event_end = $result['event_length'];
    	$event_days = strrchr($result['rec_type'], '_');
?>
		<li><a href="mobile_scheduler_edit.html?<?php echo $event_id ?>">
			
				<h3>8:00AM to 11:00AM</h3>
				<p><strong><?php echo $event_days ?></strong></p>
				
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