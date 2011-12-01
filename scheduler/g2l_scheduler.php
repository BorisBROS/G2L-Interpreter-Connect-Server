<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title></title>
</head>
	<script src="dhtmlx/dhtmlxscheduler.js" type="text/javascript" charset="utf-8"></script>
	<script src="dhtmlx/dhtmlxscheduler_recurring.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="dhtmlx/dhtmlxscheduler.css" type="text/css" title="no title" charset="utf-8">
<style type="text/css" media="screen">
	html, body{
		margin:0px;
		padding:0px;
		height:100%;
		overflow:hidden;
	}	
</style>
<script type="text/javascript" charset="utf-8">
	function init() {

		var interpreter_id = <?php
			//TODO: add a read only parameter.
			//PHP is only used to get the interpreter ID.
			if(array_key_exists('interpreter_id', $_REQUEST)){
				echo(htmlentities($_REQUEST['interpreter_id']));
			}
			else{
				echo(-1);
			}
		?>;

		if(interpreter_id < 0){
			alert("interpreter id unspecified");
			return;
		}
		
		scheduler.config.xml_date="%Y-%m-%d %H:%i";
		//http://docs.dhtmlx.com/doku.php?id=dhtmlxscheduler_v23:settings&s[]=hours&s[]=scale
		//http://docs.dhtmlx.com/doku.php?id=dhtmlxscheduler_v23:settings_format&s[]=12&s[]=hour
		scheduler.config.hour_date="%g:%i %A";
		//Limit to 9 - 5
		//Should this be a hard constraint? (i.e. actually stop the calls from happening)?
		//TODO: Resize so the layout looks nice
		scheduler.config.first_hour = 9;
		scheduler.config.last_hour = 17;

		scheduler.config.scale_hour = "%h";
		scheduler.config.details_on_create=true;
		scheduler.config.details_on_dblclick=true;
		scheduler.init('scheduler_here',null,"week");

		//http://docs.dhtmlx.com/doku.php?id=dhtmlxscheduler:custom_details_form
		//TODO: Make a custom recurring event form so that it is only weekly.
		//This section is to be used so that the interpreter ID is tracked for all events.
		scheduler.form_blocks["readonly"]={
			render:function(sns){
				return "<div class='dhx_cal_ltext' style='height:60px;'>12345&nbsp;</div>";
			},
			set_value:function(node,value,ev){
				//node.childNodes[1].value=value||"";
				//node.childNodes[4].value=ev.details||"";
				node.firstChild.value=value||"";
				var style = "none";
				node.style.display=style; // editor area
				node.previousSibling.style.display=style; //section header
				scheduler.setLightboxSize(); //correct size of lightbox
			},
			get_value:function(node,ev){
				//ev.location = node.childNodes[4].value;
				//return node.childNodes[1].value;
				return interpreter_id;
			},
			focus:function(node){
				//var a=node.childNodes[1]; a.select(); a.focus(); 
			}
		}

		// 03_connector_options.html
		scheduler.locale.labels.section_language = "Language";
		scheduler.config.lightbox.sections = [	
			{name:"description", height:200, map_to:"text", type:"textarea" , focus:true},
			{name:"language", height:21, map_to:"language_id", type:"select", 
				options:scheduler.serverList("language")},
			{name:"interpreter", height:21, map_to:"interpreter_id", type:"readonly"},
			{name:"recurring", height:115, type:"recurring", map_to:"rec_type", button:"recurring"},
			{name:"time", height:72, type:"time", map_to:"auto"}
		];

		var connector_url = "recurring_events_connector.php";
		
		scheduler.load(connector_url+"?uid="+scheduler.uid()+"&interpreter_id="+interpreter_id);
		
		var dp = new dataProcessor(connector_url+"?interpreter_id="+interpreter_id);
		dp.init(scheduler);
	}
</script>
<body onload="init();">
	<!-- <p><?php echo("[Interpreter name]'s schedule") ?></p> -->
	<div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:100%;'>
		<div class="dhx_cal_navline">
			<div class="dhx_cal_prev_button">&nbsp;</div>
			<div class="dhx_cal_next_button">&nbsp;</div>
			<div class="dhx_cal_today_button"></div>
			<div class="dhx_cal_date"></div>
			<div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>
			<div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
			<div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>
		</div>
		<div class="dhx_cal_header">
		</div>
		<div class="dhx_cal_data">
		</div>		
	</div>
</body>
