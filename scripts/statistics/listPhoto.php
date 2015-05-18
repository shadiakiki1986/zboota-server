<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/connectDynamodb.php';

// get user data
$ddb=connectDynamoDb();
$iterator=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-users',
    'ProjectionExpression' => 'email,registrationDate,lastloginDate,lpns',
    'ExpressionAttributeValues' =>  array ( ':val1' => array('S' => '"photoUrl"') ),
    'FilterExpression' => 'contains(lpns, :val1)'

));

echo sprintf("List of users with photo info (%s)\n",iterator_count($iterator));
foreach ($iterator as $item) {
    echo sprintf("%s, %s, %3u, %s\n", $item['registrationDate']['S'], $item['lastloginDate']['S'], count(json_decode($item['lpns']['S'],true)), $item['email']['S']);

}

