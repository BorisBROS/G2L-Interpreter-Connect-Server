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
	
	<label for="mydate">Some Time</label>

	<input name="mydate" id="mydate" type="date" data-role="datebox"
	   data-options='{"mode": "timebox", "timeFormatOverride": 12}'>
	
	<div data-role="content" data-theme="c">
		<div class="content-secondary"> 
	
			<div id="jqm-homeheader"> 
				<h1 id="jqm-logo"><img src="../jquery-logo-db.png" alt="jQuery Mobile Framework :: DateBox" /></h1> 
				<p>A Date and Time Picker plugin for jQueryMobile</p> 
				
			</div> 
			
			<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="f"> 
				<li data-role="list-divider">Android Demos</li> 
				<li data-theme="a">Simple Options</li> 
				<li><a href="limit.html">Range Limiting &amp; Picking</a></li>
			</ul> 
			
		</div><!--/content-primary-->	
		
		<div class="content-primary"> 
			<nav> 
				<h2>Android Default</h2>
				
				<div data-role="fieldcontain">
					<label for="defandroid">Some Date</label><input name="defandroid" type="date" data-role="datebox" id="defandroid" />
				</div>
				
				<p>Using the android mode is as simple as setting the 'mode' option to "datebox".</p>
				
				<pre class="prettyprint">&lt;label for="mydate"&gt;Some Date&lt;/label&gt;

&lt;input name="mydate" id="mydate" type="date" data-role="datebox"
   data-options='{"mode": "datebox"}'&gt;</pre>
				
				
				<h2>Credit Cards</h2>
				
				<div data-role="fieldcontain">
					<label for="droidcard">Expires</label><input name="droidcard" type="date" data-role="datebox" data-options='{"dateFormat": "mm/YYYY", "fieldsOrderOverride": ["m", "y"]}' id="droidcard" />
				</div>
				
				<p>Credit Card expiration dates can be gathered by setting 'dateFormat' to "mm/YYYY" and 'fieldsOrder' to ["m", "y"]</p>
				
				<pre class="prettyprint">&lt;label for="mydate"&gt;Some Date&lt;/label&gt;

&lt;input name="mydate" id="mydate" type="date" data-role="datebox"
   data-options='{"mode": "datebox", "dateFormat": "mm/YYYY", "fieldsOrderOverride": ["m", "y"]}'}'&gt;</pre>
				
			</nav> 
		</div> 
		
	</div>
	<div data-role="footer">
		<div data-role="controlgroup" data-type="horizontal">
			<a data-role="button" href="https://github.com/jtsage/jquery-mobile-datebox">GitHub Source</a><a data-role="button" rel='external' href="http://dev.jtsage.com/blog/">Blog</a><a data-role="button" href="mailto:jtsage+datebox@gmail.com">Contact</a><a data-role="button" href="http://jquerymobile.com/">jQueryMobile Homepage</a>
		</div>
	</div>
</div>
</html>
