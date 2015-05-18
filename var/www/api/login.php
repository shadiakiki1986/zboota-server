<?php

header("Access-Control-Allow-Origin: *");

/*
 Prepares for a new user to be added to zboota-server-users table.
 Sends an email with a link to the email being registered.

 USAGE
	CLI	php login.php email pass

	AJAX
		 $.ajax({
		    url:"http://shadi.ly/zboota-server/api/login.php",
		    type: 'POST',
		    data: {name:'fahim',pass:'abc123'},
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
	$pass=$argv[2];
} else {
	if(!array_key_exists("email",$_POST)) throw new Exception("Wrong usage of POST");
	if(!array_key_exists("pass",$_POST)) throw new Exception("Wrong usage of POST");
	$email=$_POST["email"];
	$pass=$_POST["pass"];
}

require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/loginCore.php';

if($email==""||$pass=="") {
	throw new Exception("Please enter your email and password.\n");
}

try {
	$zc=loginCore($email,$pass); // throw exception if password wrong or account blocked
	echo $zc->entry['lpns']['S']; // already json
} catch (Exception $e) {
        echo json_encode(array('error'=>$e->getMessage()));
        return;
}

