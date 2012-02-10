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

//TODO: Schedule change requests


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

		echo("Request ID: $request_id\n");

		// BDR: Moved this to a separate script so we can launch it in the 
		// background and run it as a separate thread
		// send_requests($language, $request_id);
		// TODO
		// TODO: Are we sure we want to ignore all output?
		// TODO
		exec("php send-requests-thread.php --language $language --requestid ".
			"$request_id --from $phone &> /dev/null &");

		return true;
	} else {
		return false;
	}
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

			error_log("sending call command to $requester_phone");

			send_sms_to_phone($makeCallMessage, $interpreter_phone);

			error_log("accepted");
		}

	} elseif ($firstWord == "reject") {
		error_log("rejected");
		// do nothing
		// could add logging later on but I don't know what it would be used for
		// it is already possible to infer whether an interpreter didn't accept a request
		// do we care about the excluded middle?
		// This can also cut down on the number of dismiss message sent out when an interpreter
		// accepts a request if we decide to do that.
	} elseif ($firstWord == "finished") {
		$call_duration = $explodedMsg[2];// in milliseconds

		$call_finished_update_query = "UPDATE requests
		 JOIN interpreters ON interpreters.id = requests.filled_by
		 SET `finish_recieved`=NOW(), `call_duration`=$call_duration
		 WHERE interpreters.g2lphone = '$interpreter_phone'
		 AND requests.id = $request_id";

		$call_finished_update_result = mysql_query($call_finished_update_query) or die(mysql_error());
		
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

$messageHandled = handle_interpreter_message($message, $phone);

if( !$messageHandled ) {
	$messageHandled = handle_request($message, $phone);
}

if( !$messageHandled ) {
	$failureMessage = "We cannot yet process your request.  To test our system, please text only the word 'spanish' to this number.";
	// TODO put then back in: send_sms_to_phone($failureMessage, $phone);
	error_log("Failed to parse $message");
}

//TODO: Add a way to get a list of available language names by sms.

?>
