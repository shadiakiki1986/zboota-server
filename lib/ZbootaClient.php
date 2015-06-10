<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/connectDynamodb.php';
require_once ROOT.'/lib/ZbootaClientInterface.php';
require_once ROOT.'/lib/mailSend.php';
require_once ROOT.'/lib/mailValidate.php';

class ZbootaClient implements ZbootaClientInterface {

var $email;
var $pass;
var $client;
var $entry;
var $region;

function ZbootaClient($email,$pass="",$reg=AWS_REGION) {
	$this->email=$email;
	$this->pass=$pass;
	$this->region=$reg;
}

function connect() {
	$this->client=connectDynamoDb($this->region);
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
	    'ExpressionAttributeValues'=>array( ':v1'=>array('N'=>1), ':v0'=>array('N'=>0)),
	    'UpdateExpression' => 'SET passFail = if_not_exists(passFail,:v0) + :v1'
	));
//	    'UpdateExpression' => 'ADD passFail :v1'

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

function newUser() {
	// check if valid before sending email
	if(!mailValidate($this->email)) {
		throw new Exception("Invalid email ".$this->email.".");
	}

	$this->connect();
	$this->checkEmailRegistered(false); // throws an exception if the email exists
	$this->generatePassword();

	// send email
	if(!mailSend($this->email,
		"Zboota registration",
		"Welcome to Zboota.
		Your password is ".$this->pass
	)) {
		throw new Exception("Failed to send email to ".$this->email.".");
	}

	// append to table
	$this->initiateAccount();
}

}
