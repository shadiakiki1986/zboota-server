<?php
header("Access-Control-Allow-Origin: *");

/*
 Returns a list of all zboota for a set of licence plate numbers
 
 Usage:
 	CLI
		php get.php '[{"n":"537005","a":"B"}]'
 		php get.php '[{"n":"537005","a":"B"},{"n":"387175","a":"G"}]'
 		php get.php '[{"n":"239296","a":"M"},
			{"n":"213822","a":"M"},
			{"n":"220683","a":"G"},
			{"n":"387175","a":"G"},
			{"n":"218844","a":"G"},
			{"n":"479060","a":"G"},
			{"n":"190895","a":"M"},
			{"n":"537005","a":"B"}]'
		php get.php '[{"n":"123123","a":"B","t":"Private cars","y":"2015","hp":"1 - 10"}]'
		php get.php '[{"n":"123123","a":"B","t":"Private cars","y":"2015","hp":"1 - 10"}]' true

 	Ajax

		$.ajax({
		    url:"http://shadi.ly/zboota-server/api/get.php",
		    type: 'POST',
		    data: {lpns:JSON.stringify([{"n":"537005","a":"B"},{"n":"387175","a":"G"}])},
		    success: function (data) {
		        console.log(data);
		    },
		    error: function (jqXHR, ts, et) {
		        console.log("error", ts, et);
		    }
		 });
*/

// define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../../../config.php';
require_once ROOT.'/lib/getCore.php';
require_once ROOT.'/lib/errorInLpns.php';

if(isset($argc) && $argc>=2) {
	$lpns=$argv[1];
	if($argc>=3) $force=$argv[2]; else $force="false";
} else {
	if(!array_key_exists("lpns",$_POST)) return; // this is just a ping // throw new Exception("Wrong usage of POST");
	$lpns=$_POST["lpns"];
	if(array_key_exists("force",$_POST)) $force=$_POST['force']; else $force="false";
}
$force=($force=="true");

if($lpns=="") {
	echo json_encode(array('error'=>"Please pass your set of license plate numbers."));
	return;
}

$lpns=json_decode($lpns,true);

// verify
$eilpns=errorInLpns($lpns);
if(!!$eilpns) {
	echo json_encode(array('error'=>$eilpns);
	return;
}

$data=getCore($lpns,$force);
echo json_encode($data);
