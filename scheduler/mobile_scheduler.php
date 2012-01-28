<!DOCTYPE html> 
<html lang="en"> 
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<title>Single page template</title> 
	<!-- <link rel="stylesheet" href="//code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.css" />
	<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script> -->

	
	<link type="text/css" href="http://dev.jtsage.com/cdn/datebox/latest/jquery.mobile.datebox.min.css" rel="stylesheet" /> 
	<link type="text/css" href="http://dev.jtsage.com/jQM-DateBox/css/demos.css" rel="stylesheet" /> 
	
	<!-- NOTE: Script load order is significant! -->
	
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.4.min.js"></script> 
	<script type="text/javascript" src="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.js"></script>
	<script type="text/javascript" src="http://dev.jtsage.com/jquery.mousewheel.min.js"></script>
	<script type="text/javascript" src="http://dev.jtsage.com/cdn/datebox/latest/jquery.mobile.datebox.min.js"></script>
	<script type="text/javascript" src="http://dev.jtsage.com/gpretty/prettify.js"></script>
	<link type="text/css" href="http://dev.jtsage.com/gpretty/prettify.css" rel="stylesheet" />
	
	<script type="text/javascript" charset="utf-8">
		$('div').live('pagecreate', function() {
			prettyPrint()
		});
	</script>
</head> 

<body> 

<div data-role="page">

	<div data-role="header">
		<h1>Single page</h1>
	</div><!-- /header -->

	<div data-role="content">	
	<label for="mydate">Some Time</label>

	<input name="mydate" id="mydate" type="date" data-role="datebox"
	   data-options='{"mode": "timebox", "timeFormatOverride": 12}'>
	   
		<p>This is a single page boilerplate template that you can copy to build you first jQuery Mobile page. Each link or form from here will pull a new page in via Ajax to support the animated page transitions.</p>		
		<p>Just view the source and copy the code to get started. All the CSS and JS is linked to the jQuery CDN versions so this is super easy to set up. Remember to include a meta viewport tag in the head to set the zoom level.</p>
		<p>This template is standard HTML document with a single "page" container inside, unlike a <a href="multipage-template.html" data-ajax="false">multi-page template</a> that has multiple pages within it. We strongly recommend building your site or app as a series of separate pages like this because it's cleaner, more lightweight and works better without JavaScript.</p>
	</div><!-- /content -->
	
	<div data-role="footer">
		<h4>Footer content</h4>
	</div><!-- /footer -->
	
</div><!-- /page -->

</body>
</html>