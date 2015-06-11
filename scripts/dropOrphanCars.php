<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/connectDynamodb.php';
require_once ROOT.'/lib/getZbootaUsers.php';

# drop all orphan entries in zboota-cars
$ddb=connectDynamoDb();
$lpns=getZbootaUsers($ddb);
$zc=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-cars'
));
foreach($zc as $d1) {
	if(!array_key_exists($d1['id']['S'],$lpns)) {
	    echo date("Y-m-d H:i ").": Dropping orphan car from zboota-cars: {$d1['id']['S']}.\n";
/*	    $ddb->deleteItem(array(
		'TableName' => 'zboota-cars',
		'Key' => array(
		    'id'   => array('S' => $d1['id']['S'])
		)
	    ));*/
	}
}
