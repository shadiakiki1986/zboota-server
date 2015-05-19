<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/connectDynamodb.php';

// connect
$ddb=connectDynamoDb();

// 
$unconfirmedSince=date("Y-m-d h:m",strtotime('-30 days'));

// list unconfirmed accounts
$iterator=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-users',
    'ProjectionExpression' => 'email,lastloginDate,registrationDate',
    'ExpressionAttributeValues' =>  array (
	':val1' => array('S' => $unconfirmedSince),
	':val2' => array('S' => "-")
    ),
    'FilterExpression' => 'registrationDate < :val1 AND lastloginDate = :val2' // :val2 = :val2 and :val1=:val1' //
));

// delete
foreach ($iterator as $item) {
    echo date("Y-m-d H:i")." : Deleting ".$item['email']['S']." from zboota-users because it's an uncofirmed account since {$unconfirmedSince}\n";
    $ddb->deleteItem(array(
	'TableName' => 'zboota-users',
	'Key' => array(
	    'email'   => array('S' => $item['email']['S'])
	)
    ));
}


