<?php

require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/ZbootaClient.php';

function loginCore($email,$pass) {
	$zc=new ZbootaClient($email,$pass);
	$zc->connect();
	$zc->checkEmailRegistered(); // throws an exception if the email doesn't exist
	$zc->checkPassFail(); // throws an exception if the account is locked
	$zc->checkPassword(); // throws an exception if the password doesn't match
	$zc->dropPassFail();	
	$zc->updateLastloginDate();
	return $zc;
}
