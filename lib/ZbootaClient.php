<?php

require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/connectDynamodb.php';
require_once ROOT.'/lib/ZbootaClientInterface.php';

class ZbootaClient implements ZbootaClientInterface {

var $email;
var $pass;
var $client;
var $entry;

function ZbootaClient($email,$pass="") {
	$this->email=$email;
	$this->pass=$pass;
}

function connect() {
	$this->client=connectDynamoDb();
	$this->entry=$this->client->getItem(array(
	    'TableName' => 'zboota-users',
	    'Key' => array( 'email'      => array('S' => $this->email) )
	));
	$this->entry=(array)$this->entry['Item'];

}

function checkEmailRegistered($exceptionIfNot=true) {
	if( $exceptionIfNot) if(count($this->entry)==0) throw new Exception("Email address not registered.");
	if(!$exceptionIfNot) if(count($this->entry)!=0) throw new Exception("Email address already registered.");
}

function checkPassFail() {
	$pf=0;
	if(isset($this->entry['passFail'])) {
		$pf = intval($this->entry['passFail']['N']);
	}
	if($pf>MAX_PASS_FAIL) { throw new Exception("Account locked."); }
}

function incrementPassFail() {
	$user=$this->entry;
	if(!isset($this->entry['passFail'])) $this->entry['passFail']=array('N'=>0);
	$this->entry['passFail']['N']=$this->entry['passFail']['N']+1;

	$this->client->putItem(array(
	    'TableName' => 'zboota-users',
	    'Item' => $this->entry
	));
}

function checkPassword() {
	// manage the passFail parameter for locking accounts
	if($this->entry['pass']['S']!=$this->pass) {
		$this->incrementPassFail();
		throw new Exception("Wrong password.");
	}
}

function dropPassFail() {
	unset($this->entry['passFail']);
	$this->client->putItem(array(
	    'TableName' => 'zboota-users',
	    'Item' => $this->entry
	));
}

function updateAccountNumbers($lpns) {
	$this->entry['lpns']['S']=$lpns; // overwrite existing data with given data
	$this->client->putItem(array(
	    'TableName' => 'zboota-users',
	    'Item' => $this->entry
	));
}

function updateLastloginDate() {
	$this->entry['lastloginDate']['S']=date("Y-m-d H:i:s"); // overwrite existing data with given data
	$this->client->putItem(array(
	    'TableName' => 'zboota-users',
	    'Item' => $this->entry
	));
}

function generatePassword() {
        // generate random code
        $this->pass=substr(uniqid(),-5,5);
}

function initiateAccount() {
	// append to table
	$this->client->putItem(array(
	    'TableName' => 'zboota-users',
	    'Item' => array(
		'email' => array('S' => $this->email),
		'pass'  => array('S' => $this->pass),
		'registrationDate' => array('S' => date("Y-m-d H:i:s")),
		'lastloginDate' => array('S' => "-"),
		'lpns' => array('S' => "{}")
	    )
	));

}

}
