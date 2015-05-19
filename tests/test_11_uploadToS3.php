<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaS3Client.php';

$s3=new ZbootaS3Client();
$s3->connect();
$s3->test();
$s3->listPhotos();

$f1=$s3->put("/home/shadi/Pictures/meme-todel.jpg",true,true,false);
$f2=$s3->put("/home/shadi/Pictures/meme-todel.jpg",false,false,false); // should be faster
if($f2==$f1) die("Why did I get the same filename");
if(!file_exists("/home/shadi/Pictures/meme-todel.jpg")) die("Deleted file when shouldn't have");
$f3=$s3->put("/home/shadi/Pictures/meme-todel.jpg",true,true,true);
if(file_exists("/home/shadi/Pictures/meme-todel.jpg")) die("Did not delete file when should have");

$s3->listPhotos();

$f4=$s3->get("meme.jpg");
if(!file_exists($f4)) die("Was unable to save downloaded file");

