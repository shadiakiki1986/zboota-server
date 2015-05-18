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

try {

	if($email=="") {
		throw new Exception("Please enter your email.\n");
	}


	$zc=new ZbootaClient($email);
	$zc->newUser(); // would throw an exception if email exists

	// done
	echo "{}";
} catch(Exception $e) {
	echo json_encode(array('error'=>$e->getMessage()));
}
