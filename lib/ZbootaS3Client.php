<?php
require_once dirname(__FILE__).'/../config.php';
use Aws\S3\S3Client;

class ZbootaS3Client {

protected $client;

function __construct() {
	if(!defined("S3_BUCKET")) throw new Exception("Please define the S3_BUCKET variable in the zboota-server config file");
	if(!defined("S3_FOLDER")) throw new Exception("Please define the S3_FOLDER variable in the zboota-server config file");
	if(!defined("AWS_KEY")||!defined("AWS_SECRET")||!defined("AWS_REGION")) throw new Exception("Please define your AWS properties in the zboota-server config file");

}

function connect() {
	$this->client=S3Client::factory(array(
	    'key' => AWS_KEY, # check config file
	    'secret'  => AWS_SECRET,
	    'region'  => AWS_REGION
	));
}

function test() {
	if(!$this->client->isValidBucketName(S3_BUCKET)) throw new Exception("Why did I name my S3 bucket ".S3_BUCKET."?");
	if(!$this->client->doesBucketExist(S3_BUCKET)) throw new Exception("Where is my S3 bucket?");
	if(!$this->client->doesObjectExist(S3_BUCKET,S3_FOLDER."/")) throw new Exception("Where is the photos folder?");

}

function listPhotos($returnIterator=false) {
	$iterator = $this->client->getIterator('ListObjects', array(  'Bucket' => S3_BUCKET, 'Prefix' => S3_FOLDER));

	if(!$returnIterator) {
		echo "List object keys\n";
		foreach ($iterator as $object) { echo $object['Key'] . "\n"; }
	}
	return $iterator;
}

function put($fn1,$wait=false,$test=false,$rmSourceAfterUpload=false) {
# This is a function that can move a zboota user-uploaded photo to S3 and returns the key on S3 used
#
# $fn1: File name to put to server


	if(!file_exists($fn1)) throw new Exception("Source file does not exist");
	//if(basename($fn1)!=$fn1) throw new Exception("Please only pass the filename");

	// Name of file to use on S3
	$fn2=basename($fn1);  // start with provided filename
	// if filename already exists, try another
	$attempt=0;
	while($attempt<3 && $this->client->doesObjectExist(S3_BUCKET,S3_FOLDER."/".$fn2)) {
	        $fn2  = basename(tempnam(sys_get_temp_dir(), 'f_'));
		$attempt++;
	}
	if($attempt>=3) {
		throw new Exception("Error uploading image to S3");
	}
	
	//
	$this->client->putObject(array(
		'Bucket'=>S3_BUCKET,
		'Key'=>S3_FOLDER."/".$fn2,
		'SourceFile'=>$fn1
	));
	if($wait) $this->client->waitUntil("ObjectExists",array(
		'Bucket'=>S3_BUCKET,
		'Key'=>S3_FOLDER."/".$fn2
	));
	if($test) if(!$this->client->doesObjectExist(S3_BUCKET,S3_FOLDER."/".$fn2)) throw new Exception("Error uploading image to S3");
	if($rmSourceAfterUpload) unlink($fn1);

	return $fn2;
}

function get($fn) {
# Get an uploaded image and return the path to the temporary filename where it is downloaded
#
# $fn1: Image filename on S3 to retrieve
	$tfn=tempnam(sys_get_temp_dir(), 'FOO');
	$this->client->getObject(array(
		'Bucket'=>S3_BUCKET,
		'Key'=>S3_FOLDER."/".$fn,
		'SaveAs'=>$tfn
	));
	return $tfn;
}

function backup() {
# Get base folder
	$today=date("YmdHis");
	$tfn = BKP_ZBOOTA."/bkp-photos-".$today;
	if(file_exists($tfn)) die("Folder already exists ".$tfn);
	if(!mkdir($tfn)) die("Failed to create folder ".$tfn);
	$this->client->downloadBucket(
		$tfn,
		S3_BUCKET,
		S3_FOLDER."/"
	);
	return $tfn;
}


}
