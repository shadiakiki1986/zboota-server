<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaS3Client.php';

class ZbootaS3ClientTest extends PHPUnit_Framework_TestCase
{

    public function testUpload()
    {
	$s3=new ZbootaS3Client();
	$s3->connect();
	$s3->test();

	// try to upload some photos
	$temp = tempnam(sys_get_temp_dir(), 'test_'); // "/home/shadi/Pictures/meme-todel.jpg"
	file_put_contents($temp,"something");
	$f1=$s3->put($temp,true,true,false);
	$f2=$s3->put($temp,false,false,false); // \TODO test that this line is faster than the one above
	$this->assertTrue($f2!=$f1); // shouldn't get the same filename
	$this->assertTrue(file_exists($temp)); // die("Deleted file when shouldn't have");

	$f3=$s3->put($temp,true,true,true);
	$this->assertTrue(!file_exists($temp)); // die("Did not delete file when should have");

	// check that uplaoded photos are in list
	sleep(5); // manually wait for file f1 to finish
	$xx=$s3->listPhotos(true);
	$xx=iterator_to_array($xx);
	$xx=array_map(function($yy) { return $yy["Key"]; }, $xx);
	$this->assertTrue(in_array("photos/".$f1,$xx));
	$this->assertTrue(in_array("photos/".$f2,$xx));
	$this->assertTrue(in_array("photos/".$f3,$xx));

	// try to download inexistant key
	$ff="something-that-does-not-exist.jpg";
	try {
		$f4=$s3->get($ff);
	} catch(Exception $e) {
		$this->assertTrue($e->getMessage()=="The specified key does not exist.");
	}

	// download existing key
	$f4=$s3->get($f1);
	$this->assertTrue(file_exists($f4));// die("Was unable to save downloaded file");
    }

    public function testListPhotos()
    {
	$s3=new ZbootaS3Client();
	$s3->connect();
	$xx=$s3->listPhotos(true);
	$xx=iterator_to_array($xx);
	$xx=array_map(function($yy) { return $yy["Key"]; }, $xx);
	$this->assertTrue(count($xx)>5); // there would be at least 5 photos counting the ones I uploaded
	$this->assertTrue(in_array("photos/",$xx));
	$this->assertTrue(in_array("photos/f_0h07aK",$xx));
    }
}
