<?php

header("Access-Control-Allow-Origin: *");

/*
 Prepares for a new user to be added to zboota-server-users table.
 Sends an email with a link to the email being registered.

 USAGE
	CLI	php forgotPassword.php email

	AJAX
		 $.ajax({
		    url:"http://shadi.ly/zboota-server/api/forgotPassword.php",
		    type: 'POST',
		    data: {email:'fahim@bal.adsc.com'},
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

try {
	if($email=="") {
		throw new Exception("Please enter your email.\n");
	}

	$zc=new ZbootaClient($email);
	$zc->connect();
	$zc->checkEmailRegistered(); // throws an exception if the email doesn't exist
	$zc->checkPassFail(); // throws an exception if the account is locked
	$zc->incrementPassFail(); // to avoid email flooding if this is requested more than MAX times
	$password=$zc->entry['pass']['S'];

	// send email
	if(!mailSend($email,
		"Zboota forgotten password",
		"Welcome to Zboota.
		Your password for {$email} on zboota is {$password}")
	) {
		echo json_encode(array('error'=>"Failed to send email to {$email}."));
		return;
	} else {
		echo "{}";
	}

} catch (Exception $e) {
	$lc=json_encode(array('error'=>$e->getMessage()));
	echo $lc;
}
