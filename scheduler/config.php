<?php
/*
from readme.txt:

Expected php.ini settings
	magic_quotes off
	error_level E_ALL & ~E_NOTICE
		
	
Before running samples, make sure that db connection settings in config.php are set properly and database was filled from dump.sql file

(c) dhtmlx ltd.
*/

	//Aparenly require_once paths are relative to the path of anything that this file is included in.
	require_once('../g2l_constants.php');

	$mysql_user = G2L_MYSQL_USER;
	$mysql_db = G2L_MYSQL_DB;
	$mysql_server= G2L_MYSQL_HOST;
	$mysql_pass = G2L_MYSQL_PASSWORD;

?>
