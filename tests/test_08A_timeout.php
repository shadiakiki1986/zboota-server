<?php

define("MY_CURL_TIMEOUT", 1);
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/getCore.php';

$x=getCore(array(array('a'=>"B",'n'=>"138288",'t'=>"Private cars",'hp'=>"1 - 10",'y'=>"2015")),true);
$x=$x["B/138288"];
if(assert($x["pml"]=="Not available" && $x["isf"]=="Not available" && $x["dm"]=="Not available")) echo("Test passed\n");
