<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/connectDynamodb.php';

// get user data
$ddb=connectDynamoDb();
$iterator=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-cars',
    'ProjectionExpression' => 'id,dataTs',
    'ExpressionAttributeValues' =>  array ( ':val1' => array('S' => date("Y-m-d",strtotime('-30 days')))),
    'FilterExpression' => 'dataTs < :val1'
));

// delete stale accounts
foreach ($iterator as $item) {
    echo date("Y-m-d H:i")." : Deleting stale cars ".$item['id']['S']." from zboota-cars\n";
    $ddb->deleteItem(array(
	'TableName' => 'zboota-cars',
	'Key' => array(
	    'id'   => array('S' => $item['id']['S'])
	)
    ));
}

