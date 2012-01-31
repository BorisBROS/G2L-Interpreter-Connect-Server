<?php
$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
/**
 * This is used with the regular expression for parsing rec_type to produce a list of days.
 */
function int_to_day($int){
	global $days;
	return $days[$int];
}
?>