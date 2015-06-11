<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/connectDynamodb.php';

$ddb=connectDynamoDb();
$today=date("YmdHis");
$today2=date("Y-m-d H:i:s");

function makeBkp($name) {
# name: e.g. zboota-cars
	$zc=$GLOBALS['ddb']->getIterator('Scan',array(
	    'TableName' => $name
	));
	$zc=iterator_to_array($zc);
	file_put_contents(BKP_ZBOOTA."/bkp-$name-".$GLOBALS['today'].".json",json_encode($zc));
	//var_dump(BKP_ZBOOTA."/bkp-$name-".$GLOBALS['today'].".json",json_encode($zc));
}

makeBkp("zboota-cars");
makeBkp("zboota-users");
makeBkp("zboota-notifications");

echo "$today2 : backup ddb tables complete\n";
