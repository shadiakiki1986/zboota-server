<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaS3Client.php';


$s3=new ZbootaS3Client();
$s3->connect();
$today2=date("Y-m-d H:i:s");
$tfn=$s3->backup();
echo "$today2 : backup s3 bucket photos complete: $tfn\n";
