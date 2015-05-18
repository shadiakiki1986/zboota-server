<?php

interface ZbootaClientInterface {
	function ZbootaClient($email,$pass);
	function connect();
	function checkEmailRegistered();
	function checkPassFail();
	function incrementPassFail();
	function checkPassword();
	function dropPassFail();
	function updateAccountNumbers($lpns);
	function updateLastloginDate();
	function generatePassword();
	function initiateAccount();
}
