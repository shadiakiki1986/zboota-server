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
	if(!isset($this->entry['passFail'])) $this->entry['passFail']=array('N'=>0);
	$this->entry['passFail']['N']=$this->entry['passFail']['N']+1;

	// http://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.DynamoDb.DynamoDbClient.html#_updateItem
	// http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/Expressions.Modifying.html#Expressions.Modifying.UpdateExpressions.ADD
	$this->client->updateItem(array(
	    'TableName' => 'zboota-users',
	    'Key' =>  array( 'email' => array('S' => $this->email) ),
	    'ExpressionAttributeValues'=>array( ':v1'=>array('N'=>1)),
	    'UpdateExpression' => 'SET passFail = if_not_exists(passFail,0) + :v1'
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
	$this->client->updateItem(array(
	    'TableName' => 'zboota-users',
	    'Key' =>  array( 'email' => array('S' => $this->email) ),
	    'UpdateExpression' => 'REMOVE passFail'
	));
}

function updateAccountNumbers($lpns) {
	$this->entry['lpns']['S']=$lpns; // overwrite existing data with given data
	$this->client->updateItem(array(
	    'TableName' => 'zboota-users',
	    'Key' =>  array( 'email' => array('S' => $this->email) ),
	    'ExpressionAttributeValues'=>array( ':v1'=>array('S'=>$lpns)),
	    'UpdateExpression' => 'SET lpns = :v1'
	));
}

function updateLastloginDate() {
	$this->entry['lastloginDate']['S']=date("Y-m-d H:i:s"); // overwrite existing data with given data
	$this->client->updateItem(array(
	    'TableName' => 'zboota-users',
	    'Key' =>  array( 'email' => array('S' => $this->email) ),
	    'ExpressionAttributeValues'=>array( ':v1'=>array('S'=>date("Y-m-d H:i:s"))),
	    'UpdateExpression' => 'SET lastloginDate = :v1'
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
