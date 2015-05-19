<?php

header("Access-Control-Allow-Origin: *");

# Example usage:
# php loadPhoto.php f_5575748747.jpg

require_once dirname(__FILE__).'/../../../config.php';
require_once ROOT.'/lib/ZbootaS3Client.php';

if(isset($argc) && $argc>1) {
	$name0=$argv[1];
} else {
	if(!array_key_exists("name",$_GET)) throw new Exception("Wrong usage of GET");
	$name0=$_GET["name"];
}
$name0=rtrim(ltrim($name0));
if($name0=="" ) throw new Exception("Photo: Empty name passed");

// security check
if(basename($name0)!=$name0) {
	throw new Exception("Photo with name '$name0' contains a path, and hence is a security threat");
} else {
	// open the file in a binary mode
	$s3=new ZbootaS3Client();
	$s3->connect();
	$name=$s3->get($name0);

	if(!file_exists($name)) {
		throw new Exception("Photo with name '$name' does not exist");
	} else {
		/*
		// THIS http://www.w3.org/TR/html5/scripting-1.html#security-with-canvas-elements
		$fp = fopen($name, 'rb');

		// send the right headers
		header("Content-Type: image/png");
		header("Content-Length: " . filesize($name));

		// dump the picture and stop the script
		fpassthru($fp);
		*/
		// Getting data from image file
		$file = file_get_contents($name);
		// Encode binary data to base
		echo "data:image/png;base64,".base64_encode($file);
	}
}
