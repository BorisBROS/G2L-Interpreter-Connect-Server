<?php
/**
 * 
 * This runs when voxeo passes a SMS on to us in the form of an http request.
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

//TODO: Immediate availability feature (while the phone is in available mode, one interpreter will be chosen to recieve the next request directly).

/**
 * This function attempts to detect the requested language in an sms message.
 * It either returns the language name or NULL if the language could not be detected
 */
function detect_requested_language($message) {

	$language = strtolower($message);
	$language = trim(str_replace("fwd:", "", $language));
	$escaped_language_string = mysql_real_escape_string($language);

	// TODO: partial string matches (i.e. span for spanish).

	$query = "SELECT (language_name_string) FROM languages
	           WHERE language_name_string = '$escaped_language_string'
		   OR alternate_name = '$escaped_language_string'";

	$result = mysql_query($query) or die(mysql_error());

	if (mysql_num_rows($result) == 1) {

		return $language; //TODO: use query

	} else if(mysql_num_rows($result) > 1) {
	
		error_log("To many matching languages for $language");
		
	} 
	return NULL;
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
		 AND TIMEDIFF(CURTIME(), TIME(events_rec.start_date)) >= 0
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

	//TODO: Check accept/finish activity as well.
	//	This way we can avoid sending to interpreters who are currently interpreting,
	
	//error_log('Available Interpreters Query:' . $query);

	$result = mysql_query($query) or die(mysql_error());
	$available_interpreters = mysql_num_rows($result);

	
	// I was trying to close the connection and send a reply in the http responce,
	// but now I'm doing it explicitly with send_sms_to_phone
	// Closing the connection early seems to be pretty messy.
	// Some refrences:
	// http://stackoverflow.com/questions/138374/close-a-connection-early
	// http://www.php.net/manual/en/features.connection-handling.php#71172
	// http://php.net/manual/en/function.flush.php
	ob_end_clean();
	header("Connection: close");
	ignore_user_abort();
	ob_start();
	
	if( array_key_exists('debug', $_REQUEST) ) {
		echo("Attempting to send  requests to $available_interpreters available interpreters");
	}
	//Removing accent because it doesn't work on my phone.
	//send_sms_to_phone("Tratando de enviar la petición a $available_interpreters interprete(s) disponibles.", $requester_phone_num);
	send_sms_to_phone("Tratando de enviar la peticion a $available_interpreters interprete(s) disponibles.", $requester_phone_num);
	
	$size = ob_get_length();
	header("Content-Length: $size");
	flush();
	ob_flush();
	ob_end_flush(); // Strange behaviour, will not work
	flush();		// Unless both are called !
	
	$requests_sent = 0;
	while ($row = mysql_fetch_assoc($result)) {
		//Check if the request was filled and terminate the loop if so
		//Should this loop's body be a transaction?
		$request_filled_query = "SELECT * FROM requests
		WHERE `filled_by` IS NULL AND `id` = $request_id";

		$request_filled_result = mysql_query($request_filled_query) or die(mysql_error());
		if(mysql_num_rows($request_filled_result) == 0) { //This request was filled
			break;
		}
		
		//Request isn't filled so try th next interpreter:
		$interpreter_id = $row["id"];
		$interpreter_phone = $row["g2lphone"];

		//See if the interpterer appears to be busy handling another request:
		
		//TODO: Should check if a request was accepted by them rather than sent to them.
		//TODO: Check for recent rejects
		$max_call_length = 60 * 60; //seconds
		$better_interpreter_busy_query = "SELECT * FROM requests
		WHERE `filled_by` = $interpreter_id
		AND TIMESTAMPDIFF(SECOND, `call_command_sent`, NOW()) < $max_call_length
		AND `finish_recieved` IS NULL";
		$better_interpreter_busy_result = mysql_query($better_interpreter_busy_query) or die(mysql_error());
		if(mysql_num_rows($better_interpreter_busy_result) > 0){ //This interpreter is busy
			error_log("BUSY");
			//continue; //Not sure if I should do this. Check with Adam.
		}
		else{
			error_log("NOT BUSY");
		}

/*
		//Old way of checking if they are busy is to see if they got any requests in the last 2 minutes.
		$interpreter_busy_query = "SELECT * FROM requests_sent
		WHERE `interpreter_id` = $interpreter_id
		AND TIMESTAMPDIFF(SECOND, `time-stamp`, NOW()) < 120";
		
		$interpreter_busy_result = mysql_query($interpreter_busy_query) or die(mysql_error());
		if(mysql_num_rows($interpreter_busy_result) > 0) { //This interpreter is busy
			continue;
		}
*/
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
			sleep ( 20 );//wait N seconds between requests
		}
	}
	return $requests_sent;
}
/**
 *  
 * This function tries to handle a message with the assumption it is an interpretation request.
 * @param string $message
 * SMS message contentes
 * @param string $phone
 * Phone number of sender
 * @return true for success and false for failure.
 * */
function handle_request($message, $phone) {

	$language = detect_requested_language($message);

	if ($language){
		$escaped_language_string = mysql_real_escape_string($language);

		//TODO: eventually do something about duplicate requests.

		// This a transaction to make sure we get the right id.
		mysql_query("START TRANSACTION") or die(mysql_error());

		// Error handler for the following transaction.
		// Question: Do I need to declair this every time I do a transaction or just once for the db?
		/*mysql_query("DECLARE EXIT HANDLER
    				FOR SQLEXCEPTION, SQLWARNING, NOT FOUND
        			ROLLBACK") or die(mysql_error());*/

		$query = "INSERT INTO requests (`requester_phone`, `language`) VALUES ('$phone', '$escaped_language_string')";
		$result = mysql_query($query) or die(mysql_error());

		$query2 = "SELECT LAST_INSERT_ID()";
		$result2 = mysql_query($query2) or die(mysql_error()); // TODO: do something if this fails.

		mysql_query("COMMIT") or die(mysql_error());

		$row = mysql_fetch_array($result2, 0);
		$request_id = $row[0];

		if( array_key_exists('debug', $_REQUEST) ) {
			echo("Request ID: $request_id\n");
		}

		send_requests($language, $request_id, $phone);

		return true;
	} else {
		return false;
	}
}
/**
 * This function is used for logging the responses to requests sent.
 */
function update_request_sent($accepted, $interpreter_phone, $request_id, $receive_time, $response_time){
		$update_request_sent_query = "UPDATE requests_sent
		 JOIN interpreters ON interpreters.id = `interpreter_id`
		 SET `server_receive_time`=NOW(), `receive_time`=FROM_UNIXTIME($receive_time  / 1000), `response_time`=FROM_UNIXTIME($response_time  / 1000), `accepted`=$accepted
		 WHERE `request_id` = $request_id
		 AND interpreters.g2lphone = '$interpreter_phone'";
		$update_request_sent_result = mysql_query($update_request_sent_query) or die(mysql_error());
}
/**
 *  
 * This function tries to handle a message with the assumption it was received from an interpreter.
 * @param string $message
 * SMS message contentes
 * @param string $phone
 * Phone number of sender
 * @return true for success and false for failure.
 * */
function handle_interpreter_message($message, $interpreter_phone) {
	
	$explodedMsg = explode(" ", $message);
	if(count($explodedMsg) < 2) {
		return false; // Too short
		error_log("too few args: ".$message);
	}
	$firstWord = $explodedMsg[0];
	$request_id = $explodedMsg[1]; // The request_id linking the request to our database

	//check if the message is accepting/rejecting a request by looking at the first word
	if ($firstWord == "accept") {
		// Log the message:
		update_request_sent(true, $interpreter_phone, $request_id, $explodedMsg[2], $explodedMsg[3]);
		
		// Maximum delay in minutes between the system recieving an interpretation request
		// and an accept message from an interpreter.
		$maxDelay = 5;

		// Error handler for the following transaction.
		// Question: Do I need to declair this every time I do a transaction or just once for the db?
		/*mysql_query("DECLARE EXIT HANDLER
    				FOR SQLEXCEPTION, SQLWARNING, NOT FOUND
        			ROLLBACK") or die(mysql_error());*/

		// This a transaction so we don't have to worry about concurrent accept messages.
		mysql_query("START TRANSACTION") or die(mysql_error());

		// Need to make select query to get requester phone number.
		$select_request_query = "SELECT `requester_phone` from requests_sent, requests, interpreters
		                   where `interpreter_id` = interpreters.id and `request_id` = requests.id
		                   and `request_id` = $request_id and `g2lphone` = '$interpreter_phone'
		                   and TIMESTAMPDIFF(MINUTE, `time-stamp`, NOW()) < $maxDelay
		                   and `filled_by` IS NULL";
		
		$select_request_result = mysql_query($select_request_query) or die(mysql_error());

		$row = mysql_fetch_array($select_request_result, 0);
		if(!$row){
			error_log("Request already filled, too late, or bad id");

			mysql_query("COMMIT") or die(mysql_error()); // We've only made a selection at this point so this is just to release the locks
			//Send - message back to not keep the interp waiting.
			//TODO: Make this optional.
			//char code source: http://www.obkb.com/dcljr/charstxt.html
			$dismissMessage = "%2D $request_id";
			send_sms_to_phone($dismissMessage, $interpreter_phone);
		}
		else{
			$update_request_query = "UPDATE requests
			 JOIN requests_sent rs ON requests.id = rs.request_id
			 JOIN interpreters ON interpreters.id = rs.interpreter_id
			 SET `call_command_sent`=NOW(), `filled_by`=interpreters.id
			 WHERE interpreters.g2lphone = '$interpreter_phone'
			 AND requests.id = $request_id
			 AND `filled_by` IS NULL";

			$update_request_result = mysql_query($update_request_query) or die(mysql_error());

			mysql_query("COMMIT") or die(mysql_error());

			$requester_phone = $row[0];
			$makeCallMessage = "%2A $request_id $requester_phone"; // %2A is char code for *

			error_log("sending command to call $requester_phone to $interpreter_phone");

			send_sms_to_phone($makeCallMessage, $interpreter_phone);

			error_log("accepted");
		}

	} elseif ($firstWord == "reject") {
		// Log the message:
		update_request_sent(false, $interpreter_phone, $request_id, $explodedMsg[2], $explodedMsg[3]);
		// This info could also be used to cut down on the number of dismiss message sent out when an interpreter
		// accepts a request.
		error_log("rejected");
		
	} elseif ($firstWord == "finished") {
		$call_duration = $explodedMsg[2];// in milliseconds
		
		//I accidentally reversed the start and end stamps on the apps, 
		//reading them this way will allow me to fix it eventually without worrying about backwards compatibility.
		$call_start = min($explodedMsg[3], $explodedMsg[4]);
		$call_end = max($explodedMsg[3], $explodedMsg[4]);

		$call_finished_update_query = "UPDATE requests
		 JOIN interpreters ON interpreters.id = requests.filled_by
		 SET `finish_recieved`=NOW(), `call_duration`=$call_duration, `call_start`=FROM_UNIXTIME($call_start / 1000), `call_end`=FROM_UNIXTIME($call_end  / 1000)
		 WHERE interpreters.g2lphone = '$interpreter_phone'
		 AND requests.id = $request_id";

		$call_finished_update_result = mysql_query($call_finished_update_query) or die(mysql_error());
		
		//TODO: Send please take survey message (get translations from Adam?)
		
		error_log("Finished");
		
	}
	else {
		return false;
	}
	return true;
}

/*
//I'm thinking about switching to pdo to manage the db connection:
http://stackoverflow.com/questions/4076566/how-to-detect-a-rollback-in-mysql-stored-procedure
http://www.php.net/manual/en/pdo.connections.php
http://stackoverflow.com/questions/2708237/php-mysql-transactions-examples
http://www.php.net/manual/en/pdo.query.php
try {
	$dbh = new PDO('mysql:host='.G2L_MYSQL_HOST.';dbname='.G2L_MYSQL_DB, G2L_MYSQL_USER, G2L_MYSQL_PASSWORD,
	array(PDO::ATTR_PERSISTENT => true));
	echo "Connected\n";
} catch (Exception $e) {
	die("Unable to connect: " . $e->getMessage());
}
*/
// Get a database connection since we'll definately be connecting to the database
$db_connection = get_db_connection();

$phone = htmlentities($_REQUEST['user']);
$message = htmlentities($_REQUEST['msg']);

if( array_key_exists('debug', $_REQUEST) ) {
	echo("Phone: $phone\n");
	echo("Message: $message\n");
}
error_log("Phone: $phone Message: $message");
$messageHandled = handle_interpreter_message($message, $phone);

if( !$messageHandled ) {
	$messageHandled = handle_request($message, $phone);
}

if( !$messageHandled ) {
	$failureMessage = "We cannot yet process your request.  Please text only the word 'spanish' to this number.";
	echo($failureMessage);
	#send_sms_to_phone($failureMessage, $phone);
	error_log("Failed to parse $message");
}

//TODO: Add a way to get a list of available language names by sms.

?>
