<?php

# To test with a different ROOT, uncomment the below
# define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/errorInLpns.php';

class errorInLpnsTest extends PHPUnit_Framework_TestCase
{

    public function testCatchError()
    {
	$this->assertTrue( !errorInLpns(array(array("a"=>"B","n"=>"138288"))) );
	$this->assertTrue(!!errorInLpns(array(array('a'=>"B",'n'=>"blabla"))) );
	$this->assertTrue(!!errorInLpns(array(array("a"=>"_","n"=>"138288"))) );
	$this->assertTrue(!!errorInLpns(array(array("a"=>"B","n"=>"138288"),array("a"=>"_","n"=>"138288"))) );
	$this->assertTrue(!!errorInLpns(array(array("a"=>"B","n"=>"blabla")),false) );
	$this->assertTrue( !errorInLpns(array(array("a"=>"B","n"=>"blabla")),true) );
    }

}
