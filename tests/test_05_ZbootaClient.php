<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/loginCore.php';
require_once ROOT.'/lib/connectDynamodb.php';
require_once ROOT.'/lib/getZbootaUsers.php';
require_once ROOT.'/lib/sendNotificationsCore.php';

// Tests wrong password
try{
	loginCore("shadiakiki1986@yahoo.com","0000");
} catch(Exception $e) {
    assert('$e->getMessage()=="Wrong password."');
	echo($e->getMessage()."\n");
}

// Test that account is locked after 3 failed attempts
// TODO

// Test data retrieval
var_dump(loginCore("shadiakiki1986@yahoo.com","3fe33")->entry);

// list all users
$ddb=connectDynamoDb();
var_dump(count(getZbootaUsers($ddb)));

// list required notifications to email
var_dump(sendNotificationsCore($ddb));
