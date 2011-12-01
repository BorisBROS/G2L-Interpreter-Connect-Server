<?php

require_once('g2l_constants.php');

//TODO: make key a param

/**
 * 
 * This function sends an SMS with the given text to the given phone number using Voxeo's SMS API
 * @param string $msg
 * @param string $userPhoneTo
 */
function send_sms_to_phone($msg, $userPhoneTo){

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, VOXEO_SMS_URL);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERPWD, VOXEO_USERNAME.":".VOXEO_PASSWORD);

	$data = "botkey=".VOXEO_BOTKEY."&apimethod=send&msg=".$msg."&user=".$userPhoneTo."&network=SMS&from=".VOXEO_PHONE_NUMBER;

	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);

	$xml = curl_exec($ch);

	if (curl_error($ch)) {
		$returnValue =  "ERROR ". curl_error($ch);
	} else {
		$returnValue = "SUCCESS";
	}
	curl_close($ch);    
	return $returnValue;
}
	
?>	
