<?php

header("Access-Control-Allow-Origin: *");

/*
 Updates entry with data of new license plate numbers.

 USAGE
	CLI	php update.php email pass '[{"n":"537005","a":"B","l":"Siyyarte"},{"n":"387175","a":"G","l":"Siyyarta"}]'

	AJAX
		 $.ajax({
		    url:"http://shadi.ly/zboota-server/api/update.php",
		    type: 'POST',
		    data: {email:'fahim',pass:'abc123',lpns:JSON.stringify([{"n":"537005","a":"B","l":"Siyyarte"},{"n":"387175","a":"G","l":"Siyyarta"}])},
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
	$lpns=$argv[3];
} else {
	$email=$_POST["email"];
	$pass=$_POST["pass"];
	$lpns=$_POST["lpns"];
}

if($email==""||$pass==""||$lpns=="") {
	throw new Exception("Please enter your email, password, and license plate numbers.\n");
}

require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/loginCore.php';

// test lpns is valid
$lpns2=json_decode($lpns,true);
foreach($lpns2 as $k=>$v) {
	foreach($v as $k2=>$v2) {
		if(($k2=='a'&&$v2=="")||($k2=='n'&&$v2=="")) throw new Exception("Invalid area/number pair");
		if($v2=="") unset($v[$k2]); // drop empty labels because AWS putItem would fail
	}
}

// process data
try {
	$zc=loginCore($email,$pass); // throws exception if password wrong or account blocked
	$zc->updateAccountNumbers($lpns); // overwrite existing data with given data
	echo "{}";
} catch (Exception $e) {
	echo json_encode(array('error'=>$e->getMessage()));
	return;
}
