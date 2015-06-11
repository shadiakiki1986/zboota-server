<?php

function syncCorePml($a,$n,$timeout=MY_CURL_TIMEOUT) {
 // pass timeout in seconds, and convert to milliseconds below

	$v=array('a'=>$a,'n'=>$n);

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

	curl_setopt($curl, CURLOPT_URL, "http://www.parkmeterlebanon.com/statment_of_account.aspx");
	curl_setopt($curl,CURLOPT_POST, FALSE);
	$result = curl_exec($curl);

	if(!$result) {
		curl_close($curl);
		return "Not available";
	}

	# get state variable
	$dom = new DOMDocument;
	$dom->loadHTML($result);
	$finder = new DomXPath($dom);
	$vs = trim($finder->query('//input[@id="__VIEWSTATE"]/@value')->item(0)->nodeValue);
	$ev = trim($finder->query('//input[@id="__EVENTVALIDATION"]/@value')->item(0)->nodeValue);

	# set to english
	$fields = array(
		'__EVENTTARGET'=>'',
		'__EVENTARGUMENT'=>'',
		'__VIEWSTATE'=>$vs,
		'__EVENTVALIDATION'=>$ev,
		'ctl00$btnEnglish.x'=>15,
		'ctl00$btnEnglish.y'=>13
	);
	curl_setopt($curl,CURLOPT_POST, TRUE);
	curl_setopt($curl,CURLOPT_POSTFIELDS, http_build_query($fields));
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
	$result = curl_exec($curl);

	if(!$result) {
		curl_close($curl);
		return "Not available";
	}

	# get state variable
	$dom = new DOMDocument;
	$dom->loadHTML($result);
	$finder = new DomXPath($dom);
	$vs = trim($finder->query('//input[@id="__VIEWSTATE"]/@value')->item(0)->nodeValue);
	$ev = trim($finder->query('//input[@id="__EVENTVALIDATION"]/@value')->item(0)->nodeValue);

	# send form
	$fields = array(
		'__EVENTTARGET'=>'',
		'__EVENTARGUMENT'=>'',
		'__VIEWSTATE'=>$vs,
		'__EVENTVALIDATION'=>$ev,
		'ctl00$ContentPlaceHolder1$txtCarNumber'=>$n,
		'ctl00$ContentPlaceHolder1$cmbCarType'=>$a,
		'ctl00$ContentPlaceHolder1$btnSearch'=>'SUBMIT'
	);

	curl_setopt($curl,CURLOPT_POSTFIELDS, http_build_query($fields));
	$result = curl_exec($curl);
	curl_close($curl);

	if(!$result) {
		return "Not available";
	}

	$dom = new DOMDocument;
	$dom->loadHTML($result);
	$finder = new DomXPath($dom);
	$m2 = trim($finder->query('//span[@id="ctl00_ContentPlaceHolder1_lblResult"]/b/font/text()')->item(0)->nodeValue);

	// some string manipulation
	if(in_array($m2,array("TOTAL AMOUNT: 0  LBP","THERE IS NO OUTSTANDING SURCHARGES"))) $m2="None";
	$m2=str_replace("TOTAL AMOUNT: ","",$m2);

	// return
	return $m2;
}
