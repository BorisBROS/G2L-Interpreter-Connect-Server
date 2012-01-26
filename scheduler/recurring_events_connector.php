<?php
	include ('config.php');
	include ('codebase/scheduler_connector.php');
	include ('codebase/db_sqlsrv.php');

	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
    mysql_select_db($mysql_db);


	function delete_related($action){
		global $scheduler;
		
		$status = $action->get_status();
		$type =$action->get_value("rec_type");
		$pid =$action->get_value("event_pid");
		//when serie changed or deleted we need to remove all linked events
		if (($status == "deleted" || $status == "updated") && $type!=""){
			$scheduler->sql->query("DELETE FROM events_rec WHERE event_pid='".$scheduler->sql->escape($action->get_id())."'");
		}
		if ($status == "deleted" && $pid !=0){
			$scheduler->sql->query("UPDATE events_rec SET rec_type='none' WHERE event_id='".$scheduler->sql->escape($action->get_id())."'");
			$action->success();
		}
		
	}
	function insert_related($action){
		$status = $action->get_status();
		$type =$action->get_value("rec_type");
		
		if ($status == "inserted" && $type=="none")
			$action->set_status("deleted");
	}
	
	// http://docs.dhtmlx.com/doku.php?id=dhtmlxconnector:filtering
	function filter_interpreter($filter_by){
		//error_log("filter_interpreter by interpreter id = $interpreter_id");
		if (!sizeof($filter_by->rules)) 
			$filter_by->add("interpreter_id",$interpreter_id,"=");
	}
	
/*
	//For debug purposes
	foreach($_GET as $key=>$val) { 
		error_log("Gkey: $key value: $val");
	}
	foreach($_POST as $key=>$val) { 
		error_log("Pkey: $key value: $val");
	}
*/

	$interpreter_id = htmlentities($_REQUEST['interpreter_id']);
	
	$interpreter_phone = htmlentities($_REQUEST['interpreter_phone']);
	

	$scheduler = new schedulerConnector($res);
	//$scheduler->enable_log("log.txt",true);
	$scheduler->event->attach("beforeProcessing","delete_related");
	
	if($interpreter_id){
		if(!is_numeric($interpreter_id)){
			error_log("invalid interpreter_id");
			die();
		}
		$scheduler->event->attach("beforeFilter","filter_interpreter");
	}
	
	// 03_connector_options.php
	$list = new OptionsConnector($res);
	$list->render_table("languages","id","id(value),language_name_string(label)");
	$scheduler->set_options("language", $list);

	$scheduler->event->attach("afterProcessing","insert_related");
	$scheduler->render_table("events_rec","event_id","start_date,end_date,text,rec_type,event_pid,event_length,language_id,interpreter_id");//add extras here
?>