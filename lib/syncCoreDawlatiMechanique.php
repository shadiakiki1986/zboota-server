<?php
// Example 
// var_dump(syncCoreDawlatiMechanique("B","123123","Private cars","1 - 10","2015"));

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/mapArea.php';
require_once ROOT.'/lib/checkValidDawlatiMechanique.php';

function syncCoreDawlatiMechanique($a,$n,$t,$hp,$y,$timeout=MY_CURL_TIMEOUT) {
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
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		"Cache-Control: no-cache"//,
//		"Content-type: application/x-www-form-urlencoded",
//		"User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:35.0) Gecko/20100101 Firefox/35.0"
	));
	curl_setopt($curl, CURLOPT_COOKIEJAR, $cjn );

	curl_setopt($curl, CURLOPT_URL, "http://www.dawlati.gov.lb/en/mecanique");
	curl_setopt($curl,CURLOPT_POST, FALSE);
	$result = curl_exec($curl);

	if(!$result) {
		curl_close($curl);
		return "Not available";
	}

	# extract form post target from above result
	$dom = new DOMDocument();
	// http://stackoverflow.com/a/25879444
	libxml_use_internal_errors(true);
	$dom->loadHTML($result);
	libxml_use_internal_errors(false);
	$finder = new DomXPath($dom);
	$fat1 = $finder->query('//form[@id="_dawlatimecanique_WAR_dawlatimecaniqueportlet_fm"]/@action')->item(0)->nodeValue;
	$fat2 = $finder->query('//form[@id="_dawlatimecanique_WAR_dawlatimecaniqueportlet_fm"]//input[@name="__ncforminfo"]/@value')->item(0)->nodeValue;

	# check valid inputs
	//$fat32=array("Private cars","Motorcycles","Mass public transport trucks","Taxis","Public buses & minibuses","Private transport vehicles","Other private vehicles: Ambulances, etc...");
	checkValidDawlatiMechanique($finder->query('//td[@id="zVehiculeType"]//input[@name="_dawlatimecanique_WAR_dawlatimecaniqueportlet_vehicleType"]/@value'),$t,"car type");
	checkValidDawlatiMechanique($finder->query('//select[@id="_dawlatimecanique_WAR_dawlatimecaniqueportlet_modelYear"]//option/@value'),$y,"model year");
	checkValidDawlatiMechanique($finder->query('//td[@id="zHorsePower"]//input[@name="_dawlatimecanique_WAR_dawlatimecaniqueportlet_horsePower"]/@value'),$hp,"horse power");

	/*
	$result=str_replace(">",">\n",$result);
	echo($result);
	var_dump($fat1,$fat2);
	return;
	*/

	# send form
	$fields = array(
		'_dawlatimecanique_WAR_dawlatimecaniqueportlet_modelYear'=> $y,
		'_dawlatimecanique_WAR_dawlatimecaniqueportlet_horsePower'=> $hp,
		'_dawlatimecanique_WAR_dawlatimecaniqueportlet_vehicleType'=> $t,
		'_dawlatimecanique_WAR_dawlatimecaniqueportlet_plateNumber' => $n,
		'_dawlatimecanique_WAR_dawlatimecaniqueportlet_areaCode'=> $a
/*		'__ncforminfo'=> $fat2,
		'changeLanguageWarning'=>'true',
		'_dawlatimecanique_WAR_dawlatimecaniqueportlet_go.x'=>0,
		'_dawlatimecanique_WAR_dawlatimecaniqueportlet_go.y'=>0*/
//		'submitted'=>'1',
	);
//var_dump(http_build_query($fields));
	curl_setopt($curl, CURLOPT_URL, $fat1);
	curl_setopt($curl,CURLOPT_POST, TRUE);
	curl_setopt($curl,CURLOPT_POSTFIELDS, http_build_query($fields)); // $fields);
	curl_setopt($curl, CURLOPT_REFERER, 'http://www.dawlati.gov.lb/en/mecanique');
	$result = curl_exec($curl);
	curl_close($curl);

	if(!$result) {
		return "Not available";
	}

	//$result=str_replace(">",">\n",$result);
	//echo($result);

	$dom = new DOMDocument();
	// http://stackoverflow.com/a/25879444
	libxml_use_internal_errors(true);
	$dom->loadHTML($result);
	libxml_use_internal_errors(false);
	$finder = new DomXPath($dom);

	$m0 = $finder->query('//table[@id="zResult"]/tr[1]/td[1]');
	if($m0->length > 0) {
		$m0 = trim($m0->item(0)->nodeValue);
		if($m0=="There are no results matching the specifications you've entered...") {
			//throw new Exception($m0);
			return $m0;
		} else {
			$m2 = trim($finder->query('//table[@id="zResult"]/tr[2]/td[1]/font/b/text()')->item(0)->nodeValue);
			$m2=number_format($m2);
			$m1 = trim($finder->query('//table[@id="zResult"]/tr[3]/td[1]/font/b/text()')->item(0)->nodeValue);
			$m3 = $finder->query('//table[@id="zResult"]/tr[4]/td[1]/font/b/text()');
			if($m3->length==0) {
				$m3=true;
			} else {
				$m3 = trim($finder->query('//table[@id="zResult"]/tr[4]/td[1]/font/b/text()')->item(0)->nodeValue);
				$m3 = ($m3!="Your vehicle is not subject to the Mandatory Vehicle Inspection");
			}
			// return
			$dm=array("amount"=>$m2,"month"=>$m1,"inspection"=>$m3);
			return sprintf("%s LL, due in %s, mandatory inspection: %s",$dm["amount"],$dm['month'],($dm['inspection']?"required":"not required"));
		}
	} else {
		// check if got server error on forbidden access
		$m02 = $finder->query('//h3[@class="portlet-msg-error"]');
		if($m02) {
			$m02 = trim($m02->item(0)->nodeValue);
			if($m02=="Forbidden") {
				return "Not available";
			} else {
				// Not sure what to make of this
				return "Not available"; //Ma 3am nle2e hal siyyara lyom. Please rja3 jarreb ba3d shway. Sorry :/";
			}
		} else {
			// Not sure what to make of this
			return "Not available"; //Did you enter your car specifications correctly?";
		}
	}
}


