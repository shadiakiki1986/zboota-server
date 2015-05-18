<?php

# To test with a different ROOT, uncomment the below
# define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/loginCore.php';
require_once ROOT.'/lib/connectDynamodb.php';

class loginCoreTest extends PHPUnit_Framework_TestCase {
    public function testReset() {
	// drop passFail flag so as not to interfere with demo usage
	$zc=new ZbootaClient("shadiakiki1986@yahoo.com","3fe33");
	$zc->connect();
	$zc->dropPassFail();

	// test that we can login again
	$this->assertTrue($this->testSuccess());
    }

    public function testSuccess() {
	// initialize by logging in successfully once
	// Test data retrieval
	$lc=loginCore("shadiakiki1986@yahoo.com","3fe33")->entry;
	$this->assertTrue(array_key_exists("lpns",$lc));
	return array_key_exists("lpns",$lc);
    }

    public function testFailIncrement() {
	// Test that account fail flag is incremented
	try {
		loginCore("shadiakiki1986@yahoo.com","0000");
	} catch (Exception $e) {
	    $this->assertTrue($e->getMessage()=="Wrong password.");
	}
	$zc=new ZbootaClient("shadiakiki1986@yahoo.com","0000");
	$zc->connect();
	$this->assertTrue($zc->entry['passFail']['N']=="1");
    }

    public function testFailDrop() {
	// Test that the fail flag is dropped after a successful login
	loginCore("shadiakiki1986@yahoo.com","3fe33");
	$zc=new ZbootaClient("shadiakiki1986@yahoo.com","3fe33");
	$zc->connect();
	$this->assertTrue(!array_key_exists("passFail",$zc->entry));
    }


    public function testFailLock() {
	// Test that account is locked after 3? failed attempts
	try { loginCore("shadiakiki1986@yahoo.com","0000"); } catch (Exception $e) { }
	try { loginCore("shadiakiki1986@yahoo.com","0000"); } catch (Exception $e) { }
	try { loginCore("shadiakiki1986@yahoo.com","0000"); } catch (Exception $e) { }
	try { loginCore("shadiakiki1986@yahoo.com","0000"); } catch (Exception $e) { }
	try{
		loginCore("shadiakiki1986@yahoo.com","0000");
	} catch(Exception $e) {
	    $this->assertTrue($e->getMessage()=="Account locked.");
	}
    }

    public function testReset2() { $this->testReset(); }

}
