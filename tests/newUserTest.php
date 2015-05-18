<?php

# To test with a different ROOT, uncomment the below
# define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/ZbootaClient.php';

class newUserTest extends PHPUnit_Framework_TestCase {

    public function testExisting() {
	// Test that existing email throws error
	try {
		$zc=new ZbootaClient("shadiakiki1986@yahoo.com");
		$pass=$zc->newUser();
	} catch (Exception $e) {
	    $this->assertTrue($e->getMessage()=="Email address already registered.");
	}
    }

}
