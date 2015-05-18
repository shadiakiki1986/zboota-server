<?php
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/getCore.php';

class getCoreTest extends PHPUnit_Framework_TestCase
{

    public function testKeys()
    {
	$x1=getCore(array(array("a"=>"B","n"=>"138288")));
	$x2=getCore(array(array('a'=>"B",'n'=>"138288",'t'=>"Private cars",'hp'=>"1 - 10",'y'=>"2015")));
	$x3=getCore(array(array("a"=>"B","n"=>"138288")));

	$this->assertTrue(array_key_exists("B/138288",$x1));
	$this->assertTrue(array_key_exists("B/138288",$x2));
	$this->assertTrue(array_key_exists("B/138288",$x3));

	$this->assertTrue(!array_key_exists("dm",$x1["B/138288"]));
	$this->assertTrue(array_key_exists("dm",$x2["B/138288"]));
	$this->assertTrue(!array_key_exists("dm",$x3["B/138288"]));

    }

}
