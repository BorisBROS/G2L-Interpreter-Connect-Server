<?php
/**
 * 
 * This script should be used as a thread started by voxeo-sms.php
 * The purpose of it is to asynch send requests to available interpreters
 * so that the voxeo request is not blocking on this code.
 */
require_once('voxeo-sms-lib.php');
require_once('g2l_constants.php');

/**
 * @deprecated
 * Connect to the database server, and select our database.
 * @return the connection resource
 */
function get_db_connection() {

	$conn = mysql_pconnect(G2L_MYSQL_HOST, G2L_MYSQL_USER, G2L_MYSQL_PASSWORD) or die(mysql_error());
	mysql_select_db(G2L_MYSQL_DB) or die(mysql_error());
	return $conn;

}

/**
 * 
 * Sent requests with the given id to all available interpreters that speak the given language.
 * @param string $language
 * The language is used to select the correct type of interpreters from the database
 * and it also appears in the request message sent to the interpreter.
 * @param integer $request_id
 * The request id is needed so we know which request the interpreter is replying to
 * and it provides a bit of security to the system as well.
 * @param string $requester_phone_num
 * The phone number of the person requesting interpretation services. Used to send an SMS back
 * about how many interpreters are available.
 * @return
 * number of requests sent
 */
function send_requests($language, $request_id, $requester_phone_num) {
	
	$escaped_language_string = mysql_real_escape_string($language);
	/*
	This ugly beast is used to select currently ocurring events from possibly recurring events.
	Some notes about the query:
	- In the case of recurring events it only works for weekly events without parents or children (that recur indefinately).
	- SEC_TO_TIME might throw warnings for extremely long events, I don't think we need to worry about that though.
	- Our rows are returned in random order, so the same interpreter doesn't get first dibs.
	*/
	$query = "SELECT DISTINCT interpreters.id, interpreters.g2lphone FROM interpreters
	JOIN events_rec ON interpreters.id = events_rec.interpreter_id
	WHERE `active` = true
	AND (`language1` = '$escaped_language_string' OR `language2` = '$escaped_language_string')
	AND NOW() > events_rec.start_date AND NOW() < events_rec.end_date
	AND (events_rec.rec_type=''
	     OR (TIMEDIFF(CURTIME(), TIME(events_rec.start_date)) < SEC_TO_TIME(events_rec.event_length)
		 AND LOCATE('week', events_rec.rec_type) > 0
		 AND LOCATE(DAYOFWEEK(CURDATE()) - 1,
		            SUBSTRING_INDEX(SUBSTR(events_rec.rec_type,
		                                   CHAR_LENGTH(events_rec.rec_type) - LOCATE('_', REVERSE(events_rec.rec_type)) + 2),
	                                    '#', 1)) > 0))
	ORDER BY RAND()";

	/*
	Simple version without scheduling:
	$query = "SELECT `id`, `g2lphone` FROM interpreters
	WHERE `active` = true
	AND (`language1` = '$escaped_language_string' OR `language2` = '$escaped_language_string')";
	*/

	error_log('Available Interpreters Query:' . $query);

	//TODO: Maybe add a clause to see if they recently rejected a request?
	//(this would be a reason to do something with reject messages)

	$result = mysql_query($query) or die(mysql_error());
	$available_interpreters = mysql_num_rows($result);
	$requests_sent = 0;

	// TODO
	// TODO: use voxeo to send this message back to 
	// TODO: i18n this message so it sends back in the correct language. (for now, usually spanish) 
	// TODO
	echo("Attempting to send  requests to $available_interpreters available interpreters");
	// TODO: Send to $requester_phone_num
	

	// Send a message to each available interpreter 
	while ($row = mysql_fetch_assoc($result)) {

		$interpreter_id = $row["id"];
		$interpreter_phone = $row["g2lphone"];

		
		//See if a request was sent to interpreter in the last 5 minutes...

		//TODO: Track accept/finish activity as well.
		//	This way we can avoid sending to interpreters who are currently interpreting,

		//Should this be a transaction?
		$interpreter_busy_query = "SELECT * FROM requests_sent
		WHERE `interpreter_id` = $interpreter_id
		AND TIMESTAMPDIFF(SECOND, `time-stamp`, NOW()) < 120";

		$interpreter_busy_result = mysql_query($interpreter_busy_query) or die(mysql_error());

		if(mysql_num_rows($interpreter_busy_result) > 0) { //This interpreter is busy
			continue;
		}

		error_log("sending request to " . $row["g2lphone"]);

		// %2B is char code for +, which means request interpretation
		$requestSMS = '%2B ' . $request_id . ' ' . ucfirst($language);
		send_sms_to_phone($requestSMS, $interpreter_phone);

		//Before doing the rest we might want to check that the the sms_sending was a success.

		$request_sent_query = "INSERT INTO requests_sent (`request_id`, `interpreter_id`) VALUES ($request_id, $interpreter_id)";
		$result2 = mysql_query($request_sent_query) or die(mysql_error());

		$requests_sent++;

		// Throttling... 
		if($requests_sent < $available_interpreters){
			sleep ( 30 );//wait 30 seconds between requests
		}
	}
	return $requests_sent;
}


/**
 * BEGIN Script execution
 * 
 */

// Get a database connection since we'll definately be connecting to the database
$db_connection = get_db_connection();

// Process the options for this script. Not going to allow short ops. 
$shortopts  = "";
$longopts  = array(
	"language:",     // Required value
	"requestid:",    // Required value
	"from:",         // Required value
);

$options = getopt($shortopts, $longopts);

// TODO
// TODO: Error checking to make sure that all of the options exist
// TODO

send_requests($options["language"], $options["requestid"], $options["from"]);

?>