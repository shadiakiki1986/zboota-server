<?php

header("Access-Control-Allow-Origin: *");

/*
 Prepares for a new user to be added to zboota-server-users table.
 Sends an email with a link to the email being registered.

 USAGE
	CLI	php new.php email

	AJAX
		 $.ajax({
		    url:"http://shadi.ly/zboota-server/api/new.php",
		    type: 'POST',
		    data: {email:'fahim@bla.com'},
		    success: function (data) {
			console.log(data);
		    },
		    error: function (jqXHR, ts, et) {
			console.log("error", ts, et);
		    }
		 });
*/

if(isset($argc) && $argc>1) {
	$email=$argv[1];
} else {
	$email=$_POST["email"];
}

require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/ZbootaClient.php';
require_once ROOT.'/lib/mailSend.php';
require_once ROOT.'/lib/mailValidate.php';

try {

	if($email=="") {
		throw new Exception("Please enter your email.\n");
	}

	// check if valid before sending email
	if(!mailValidate($email)) {
		throw new Exception("Invalid email {$email}.");
	}

	$zc=new ZbootaClient($email);
	$zc->connect();
	$zc->checkEmailRegistered(false); // throws an exception if the email exists
	$zc->generatePassword();
	$pass=$zc->pass;

	// send email
	if(!mailSend($email,
		"Zboota registration",
		"Welcome to Zboota.
		Your password is {$pass}"
	)) {
		echo json_encode(array('error'=>"Failed to send email to {$email}."));
		return;
	}

	// append to table
	$zc->initiateAccount();

	// done
	echo "{}";
} catch(Exception $e) {
	echo json_encode(array('error'=>$e->getMessage()));
}
