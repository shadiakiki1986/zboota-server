<?php
// Test that getCore will still update with the configured MY_CURL_TIMEOUT
// including the case where dynamodb stored Not available after test_08A

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/getCore.php';

echo "Make sure you're running this after running test_08A\n";
$x=getCore(array(array('a'=>"B",'n'=>"138288",'t'=>"Private cars",'hp'=>"1 - 10",'y'=>"2015")),false)["B/138288"];
if(assert($x["pml"]!="Not available" && $x["isf"]!="Not available" && $x["dm"]!="Not available")) echo("Test passed\n");
