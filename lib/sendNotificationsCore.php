<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/getZbootaUsers.php';

function sendNotificationsCore($ddb) {
# ddb: returned by connectDynamoDb()

// get car data from cache
// Note that this uses the "implied" emails set from "syncAll.php"
$sc1=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-cars'
));
// flatten format from AWS
$sc=array();
foreach($sc1 as $k=>$v) {
	$sc[$k]=array();
	foreach($v as $k2=>$v2) $sc[$k][$k2]=$v2['S'];
}

// append implied emails
$lpns=getZbootaUsers($ddb);
foreach($sc as $k=>$v) {
	if(array_key_exists($v['id'],$lpns)) {
		$sc[$k]['emails']=$lpns[$v['id']]['emails'];
	}
}

// check for violations and prepare to email users
$notices=array();
foreach($sc as $d1) {
	if(array_key_exists('emails',$d1) && ( $d1['isf']!="None" || $d1['pml']!='None') ) {
		$emails=json_decode($d1['emails']);
		foreach($emails as $d2) {
			if(!isset($notices[$d2])) $notices[$d2]=array();
			array_push($notices[$d2],$d1['id']);
		}
	}
}

// drop entries in $notices that are also in zboota-notifications with the same set of car ids to notify
// i.e. already notified about tickets
// also, update entries in $notices whose car ids to notify about have changed
// also, drop entries in zboota-notifications that are not in $notices anymore
// i.e. if a user closes all outstanding tickets
$zn=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-notifications'
));
foreach($zn as $n1) {
	if(array_key_exists($n1['email']['S'],$notices)) {
		sort($notices[$n1['email']['S']]);
		if($n1['carIds']['S']==json_encode($notices[$n1['email']['S']],true)) {
			unset($notices[$n1['email']['S']]);
		}
	} else {
	    $ddb->deleteItem(array(
		'TableName' => 'zboota-notifications',
		'Key' => array(
		    'email'   => array('S' => $n1['email']['S'])
		)
	    ));
	}
}

return $notices;
}

