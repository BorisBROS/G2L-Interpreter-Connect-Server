<!DOCTYPE html>
<html>
	<head>
		<meta  name = "viewport" content = "initial-scale = 1.0, maximum-scale = 1.0, user-scalable = no">

		<script src="dhtmlx/dhtmlxscheduler_mobile.js" type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" href="dhtmlx/dhtmlxscheduler_mobile.css">

		<title>Event handling: add, delete, edit</title>
		<script type="text/javascript" charset="utf-8">
			//scheduler.config.init_date = new Date(2011,4,1);
			
			var uid = 1327694098481; //"scheduler"

			scheduler.config.form = [
			             			{view:"text",		label:"Event",	name:'text' },
			             			{view:"datepicker",	label:"Start",	name:'start_date',	timeSelect:1, dateFormat:dhx.i18n.fullDateFormat},
			             			{view:"datepicker",	label:"End",	name:'end_date',	timeSelect:1, dateFormat:dhx.i18n.fullDateFormat},
			             			//custom section in form
			             			{view:"text",		label:"rec_type",	name: 'rec_type'},
			             			{view:"text",		label:"event_pid",	name: 'event_pid'},
			             			{view:"text",		label:"event_length",	name: 'event_length'},
			             			{view:"text",		label:"language_id",	name: 'language_id'},
			             			{view:"text",		label:"interpreter_id",	name: 'interpreter_id'},
			             			//button can be removed
			             			{view:"button", label:"Delete", name:"delete", id:"delete"}
			             		];
			
			dhx.ready(function(){
				dhx.ui.fullScreen();
    			dhx.ui({
					view: "scheduler",
					id: "scheduler",
					save: "recurring_events_connector.php"
				});
				$$("scheduler").load("recurring_events_connector.php?uid=" + uid, "scheduler");
			});

		</script>
</head>
	<body>
	</body>
</html>