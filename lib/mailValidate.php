<?php

require_once dirname(__FILE__).'/../config.php';

function mailValidate($email) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://api.mailgun.net/v2/address/validate?address={$email}");
	curl_setopt($curl, CURLOPT_USERPWD, "api:".MAILGUN_PUBLIC_KEY);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$result = curl_exec($curl);
	curl_close($curl);

	$r=json_decode($result,true);
	return $r['is_valid'];
}
