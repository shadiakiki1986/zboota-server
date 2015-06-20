<?php
require_once dirname(__FILE__).'/../config.php';

class WebAvailability {

var $res;
function __construct() {
	$this->res=array(
		"dawlati"=>$this->ping("http://www.dawlati.gov.lb/en/mecanique"),
		"isf"=>$this->ping("http://apps.isf.gov.lb/speedticket/speedticket_en.php"),
		"pml"=>$this->ping("http://www.parkmeterlebanon.com/statment_of_account.aspx")
	);
}

function ping($url,$timeout=MY_CURL_TIMEOUT) {
    // http://stackoverflow.com/a/4607776
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout*1000);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout*1000);

    curl_exec($ch);
    $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return (200==$retcode);
}


}
