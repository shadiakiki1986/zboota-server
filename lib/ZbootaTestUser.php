<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/connectDynamodb.php';
require_once ROOT.'/lib/ZbootaClient.php';

class ZbootaTestUser {

	var $client;

	function __construct() {
		$this->client=connectDynamoDb();
	}

	function deleteTestUser() {
		$this->client->deleteItem(array(
		    'TableName' => 'zboota-users',
		    'Key' => array( 'email'      => array('S' => "shadi_akiki_1986@hotmail.com") )
		));
	}

	function exists() {
		$i2=$this->client->getItem(array(
		    'TableName' => 'zboota-users',
		    'Key' => array( 'email'      => array('S' => "shadi_akiki_1986@hotmail.com") )
		));
		return(count($i2['Item'])!=0);
	}

	function create() {
		if($this->exists()) {
			throw new Exception("Already exists");
		} else {
			$zc=new ZbootaClient("shadi_akiki_1986@hotmail.com");
			$zc->connect();
			// create user
			$zc->newUser();
		}
	}

	function password() {
		if(!$this->exists()) {
			throw new Exception("Doesn't exist");
		} else {
			$i2=$this->client->getItem(array(
			    'TableName' => 'zboota-users',
			    'Key' => array( 'email'      => array('S' => "shadi_akiki_1986@hotmail.com") )
			));
			return $i2['Item']['pass']['S'];
		}
	}

}
