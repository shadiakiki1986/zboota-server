<?php

# To test with a different ROOT, uncomment the below
# define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaClient.php';

class ZbootaClientTest extends PHPUnit_Framework_TestCase
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

	$zc=new ZbootaClient("shadiakiki1986@hotmail.com");
	$zc->connect();
	if(count($zc->entry)==0) {
		// create user
		$zc->newUser();
	}
	$zc->connect();
	$this->assertTrue(count($zc->entry)!=0); // exists now for sure
	$zc->deleteTestUser();
	$zc->connect();
	$this->assertTrue(count($zc->entry)==0); // exists not
    }

}
