<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaTestUser.php';

class ZbootaTestUserTest extends PHPUnit_Framework_TestCase
{

    public function testDeleteTestUser()
    {
	$ztu=new ZbootaTestUser();
	if(!$ztu->exists()) {
		$ztu->create();
	}
	$this->assertTrue($ztu->exists()); // exists now for sure
	$this->assertTrue(strlen($ztu->password())==5);

	$ztu->deleteTestUser();
	$this->assertTrue(!$ztu->exists()); // exists now for sure
    }

}
