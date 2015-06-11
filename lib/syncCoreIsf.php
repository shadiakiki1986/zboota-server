<?php

function syncCoreIsf($a,$n,$timeout=MY_CURL_TIMEOUT) {
 // pass timeout in seconds, and convert to milliseconds below

	# Get cookie
	$cjn=tempnam('/tmp','cookie');

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, $timeout*1000);
	curl_setopt($curl, CURLOPT_TIMEOUT_MS, $timeout*1000);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl, CURLOPT_FRESH_CONNECT, TRUE);
	curl_setopt($curl, CURLOPT_HEADER, FALSE);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Cache-Control: no-cache"));
	curl_setopt($curl, CURLOPT_COOKIEJAR, $cjn );

	curl_setopt($curl, CURLOPT_URL, "http://apps.isf.gov.lb/speedticket/speedticket_en.php");
	curl_setopt($curl,CURLOPT_POST, FALSE);
	$result = curl_exec($curl);

	if(!$result) {
		curl_close($curl);
		return "Not available";
	}

	# send form
	$fields = array(
			'platenumber' => $n,
			'carCode'=> $a,
			'submitted'=>'1',
			'Search.x'=>0,
			'Search.y'=>0
	);
	curl_setopt($curl,CURLOPT_POST, TRUE);
	curl_setopt($curl,CURLOPT_POSTFIELDS, $fields);
	$result = curl_exec($curl);
	curl_close($curl);

	if(!$result) {
		return "Not available";
	}

	$dom = new DOMDocument();
	// http://stackoverflow.com/a/25879444
	libxml_use_internal_errors(true);
	$dom->loadHTML($result);
	libxml_use_internal_errors(false);
	$finder = new DomXPath($dom);
//	$m1 = trim($finder->query('//tr[@style="color:blue;font-size:16px;"]/td/b/text()')->item(0)->nodeValue);
	$m2 = $finder->query('//tr[@style="color:green;font-size:20px;"]/td/b/text()');
	if(!$m2->length) {
		$m2 = $finder->query('//tr[@style="background-color:#E1E6EB;"]/td[2]/text()');
	}
	$m2 = trim($m2->item(0)->nodeValue);

/*	if($m1!="Vehicle number : {$n} / {$a}") {
		throw new Exception("zboota-server API needs updating for ISF tickets. {$m1} vs {$n} {$a}");
	}
*/

	// some string manipulation
	if($m2=="No violation") $m2="None";

	// return
	return $m2;
}
