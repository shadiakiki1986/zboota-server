<?php

header("Access-Control-Allow-Origin: *");

/*
 Same as login.php, but with GET method instead of POST for usage in excel sheet (web data)

 USAGE
	CLI	php login2.php email pass

	AJAX
		 $.ajax({
		    url:"http://shadi.ly/zboota-server/api/login2.php?name=fahim&pass=abc123",
		    type: 'GET',
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
	if(!array_key_exists("email",$_GET)) throw new Exception("Wrong usage of POST");
	if(!array_key_exists("pass",$_GET)) throw new Exception("Wrong usage of POST");

	$email=$_GET["email"];
	$pass=$_GET["pass"];
}

require_once dirname(__FILE__).'/../../../config.php';
require_once ROOT.'/lib/loginCore.php';
require_once ROOT.'/lib/getCore.php';

try {
	if($email==""||$pass=="") {
		throw new Exception("Please enter your email and password.\n");
	}

	$zc=loginCore($email,$pass); // throw exception if password wrong or account blocked
	$data = json_decode($zc->entry['lpns']['S'],true); // already json
	$data2=getCore($data);
	# merge
	$data3=array();
	foreach($data as $k=>$v) {
		$o=array_merge($data[$k],$data2[$k]);
		$o=array_intersect_key($o, array_flip(array('a','n','l','isf','pml')));
		$data3[$k]=$o;
	}

	$tr=array();
	foreach($data3 as $x) {
		$td=array();
		foreach($x as $y) {
			array_push($td,sprintf("<td>%s</td>",$y));
		}
		array_push($tr,sprintf("<tr>%s</tr>",implode($td)));
	}
	$table=sprintf("<table><tr><th>Letter</th><th>Number</th><th>Label</th><th>ISF</th><th>PML</th></tr>%s</table>",implode($tr));
	echo $table;
} catch (Exception $e) {
	echo json_encode(array('error'=>$e->getMessage()));
}
