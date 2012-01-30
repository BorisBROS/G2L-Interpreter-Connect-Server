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
<div data-role="page" data-theme="c" id="droIndex"> 
	<div data-role="header"> 
		<h1>jQueryMobile - DateBox</h1>
		<a href="../" data-icon="home" data-iconpos="notext">Home</a>
	</div>
	

	
	<div data-role="content" data-theme="c">
    
        <form action="forms-sample-response.php" method="post">
    
	    	<nav>
			    
                <p>I will be available...</p>

                <div data-role="fieldcontain">
                    <label for="at-date">at</label>
                    <input name="at-date" id="mydate" type="date" data-role="datebox" data-options='{"mode": "timebox", "timeFormatOverride": 12}' class="custom" />
			    </div>
				
                <div data-role="fieldcontain">
                    <label for="until-date">until</label>
                    <input name="until-date" id="mydate" type="date" data-role="datebox" data-options='{"mode": "timebox", "timeFormatOverride": 12}' />
    		    </div>
                
			</nav> 
    

    		<div  data-role="fieldcontain">
			 	<fieldset data-role="controlgroup">
					<legend>on</legend>
					<input type="checkbox" name="checkbox-1a" id="checkbox-1a" class="custom" />
					<label for="checkbox-1a">Monday</label>

					<input type="checkbox" name="checkbox-2a" id="checkbox-2a" class="custom" />
					<label for="checkbox-2a">Tuesday</label>
					
					<input type="checkbox" name="checkbox-3a" id="checkbox-3a" class="custom" />
					<label for="checkbox-3a">Wednesday</label>

					<input type="checkbox" name="checkbox-4a" id="checkbox-4a" class="custom" />
					<label for="checkbox-4a">Thursday</label>
					
					<input type="checkbox" name="checkbox-5a" id="checkbox-5a" class="custom" />
					<label for="checkbox-5a">Friday</label>
					
			    </fieldset>
			</div>
            <button type="submit" data-theme="b" name="submit" value="submit-value">Submit</button>

        
		</form>
        
	</div>
</div>
</body>
</html>
