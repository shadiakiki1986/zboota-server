<?php

# To test with a different ROOT, uncomment the below
# define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaTestUser.php';

class ZbootaTestUserTest extends PHPUnit_Framework_TestCase
{

    public function testDeleteTestUser()
    {
	if(AWS_REGION!="us-west-2") {
		// Stop here and mark this test as incomplete.
		$this->markTestIncomplete(
		 "Please only run this test in us-west-2.\n
		  Refer to config.php in the root folder."
		);
	}

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
