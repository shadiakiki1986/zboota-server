<?php

# To test with a different ROOT, uncomment the below
# define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaClient.php';

class newUserTest extends PHPUnit_Framework_TestCase {

    public function testCreate() {
	// if user exists, remove it to test create
	$zc=new ZbootaClient("shadiakiki1986@yahoo.com","dummy","us-east-1");
	$zc->connect();
	if(count($zc->entry)>0) {
		// delete the added entry so that the test can run 
		$zc->client->deleteItem(array(
		    'TableName' => 'zboota-users',
		    'Key' => array( 'email'      => array('S' => "shadiakiki1986@yahoo.com") )
		));
	}

	// create user
	$zc=new ZbootaClient("shadiakiki1986@yahoo.com","","us-east-1");
	$zc->newUser();

	// test that user was created
	$zc->connect();
	$this->assertTrue(array_key_exists("email",$zc->entry));
	$this->assertTrue(array_key_exists("pass",$zc->entry));
    }

    /**
     * @depends testCreate
     */
    public function testExisting() {
	// Test that existing email throws error
	try {
		$zc=new ZbootaClient("shadiakiki1986@yahoo.com","","us-east-1");
		$zc->newUser();
	} catch (Exception $e) {
	    $this->assertTrue($e->getMessage()=="Email address already registered.");
	}
    }

}
