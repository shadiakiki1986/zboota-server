<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../../config.php';
require_once ROOT.'/lib/connectDynamodb.php';

// get user data
$ddb=connectDynamoDb();
$iterator=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-users',
    'ProjectionExpression' => 'email,registrationDate,lastloginDate,lpns',
    'ExpressionAttributeValues' =>  array ( ':val1' => array('S' => '"hp"'), ':val2' => array('S' => '"y"'), ':val3' => array('S' => '"t"') ),
    'FilterExpression' => 'contains(lpns, :val1) and contains(lpns, :val2) and contains(lpns, :val3)'

));

echo sprintf("List of users with mechanique info (%s)\n",iterator_count($iterator));
foreach ($iterator as $item) {
    echo sprintf("%s, %s, %3u, %s\n", $item['registrationDate']['S'], $item['lastloginDate']['S'], count(json_decode($item['lpns']['S'],true)), $item['email']['S']);

}

