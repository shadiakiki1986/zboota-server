<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaS3Client.php';

$s3=new ZbootaS3Client();
$s3->connect();
$s3->listPhotos();

