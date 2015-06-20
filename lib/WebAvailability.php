<?php
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/connectDynamodb.php';

class WebAvailability {

var $res;
var $ddb;
function __construct() {
	$this->ddb=connectDynamoDb();
	$this->res=array(
		"dawlati"=>$this->ddbGet("dawlati"),
		"isf"=>$this->ddbGet("isf"),
		"pml"=>$this->ddbGet("pml")
	);
}

function refresh() {
	$res=array(
		"dawlati"=>$this->ping("http://www.dawlati.gov.lb/en/mecanique"),
		"isf"=>$this->ping("http://apps.isf.gov.lb/speedticket/speedticket_en.php"),
		"pml"=>$this->ping("http://www.parkmeterlebanon.com/statment_of_account.aspx")
	);
	$this->ddbSet("dawlati",$res["dawlati"]);
	$this->ddbSet("isf",$res["isf"]);
	$this->ddbSet("pml",$res["pml"]);
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

function ddbSet($srcId,$status) {
	$this->ddb->putItem(array(
	    'TableName' => 'zboota-availability',
	    'Item' => array(
		'srcId'   => array('S' => $srcId),
		'statusDate' => array('S'=>date("Y-m-d")),
		'statusVal'   => array('N' => $status?1:0)
	     )
	));
}

function ddbGetCore($srcId) {
	return $this->ddb->getItem(array(
	    'TableName' => 'zboota-availability',
	    'Key' => array(
		'srcId'   => array('S' => $srcId)
	     )
	));
}

function ddbGet($srcId) {
	$o = $this->ddbGetCore($srcId);
	if(count($o)==0) {
		$this->refresh();
		$o = $this->ddbGetCore($srcId);
		if(count($o)==0) throw new Exception("Error in server availability module");
	}
	$o=(array)$o['Item'];

	if($o['statusDate']['S']<date("Y-m-d")) {
		$this->refresh();
		$o = $this->ddbGetCore($srcId);
		$o=(array)$o['Item'];
		if($o['statusDate']['S']<date("Y-m-d")) throw new Exception("Error in server availability module");
	}

	return($o['statusVal']['N']==1);
}

} // end class
