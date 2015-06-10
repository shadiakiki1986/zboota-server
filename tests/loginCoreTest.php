<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/loginCore.php';
require_once ROOT.'/lib/connectDynamodb.php';

$pass="dummy";

class loginCoreTest extends PHPUnit_Framework_TestCase {

    protected $backupGlobals = FALSE;

    public function testReset() {
	// drop passFail flag so as not to interfere with demo usage
	$zc=new ZbootaClient("shadiakiki1986@yahoo.com","dummy");
	$zc->connect();
	$zc->dropPassFail();

	// set the password for other tests
	$GLOBALS['pass']=$zc->entry['pass']['S']; 
    }

    /**
     * @depends testReset
     */
    public function testSuccess() {
	// initialize by logging in successfully once
	// Test data retrieval

	$lc=loginCore("shadiakiki1986@yahoo.com",$GLOBALS['pass'])->entry;
	$this->assertTrue(array_key_exists("lpns",$lc));
    }

    /**
     * @depends testSuccess
     */
    public function testFailIncrement() {
	// Test that account fail flag is incremented
	try {
		loginCore("shadiakiki1986@yahoo.com","0000");
	} catch (Exception $e) {
	    $this->assertTrue($e->getMessage()=="Wrong password.");
	}
	$zc=new ZbootaClient("shadiakiki1986@yahoo.com","dummy");
	$zc->connect();
	$this->assertTrue(array_key_exists("passFail",$zc->entry));
	$this->assertTrue($zc->entry['passFail']['N']=="1");
    }

    /**
     * @depends testFailIncrement
     */
    public function testFailDrop() {
	// Test that the fail flag is dropped after a successful login
	loginCore("shadiakiki1986@yahoo.com",$GLOBALS['pass']);
	$zc=new ZbootaClient("shadiakiki1986@yahoo.com","dummy");
	$zc->connect();
	$this->assertTrue(!array_key_exists("passFail",$zc->entry));
    }

    /**
     * @depends testFailDrop
     */
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

    /**
     * @depends testFailLock
     */
    public function testReset2() { $this->testReset(); }

}
