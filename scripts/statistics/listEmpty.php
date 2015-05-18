<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/connectDynamodb.php';

// get user data
$ddb=connectDynamoDb();
$iterator=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-users',
    'ProjectionExpression' => 'email,registrationDate,lastloginDate,lpns',
    'ExpressionAttributeValues' =>  array ( ':val1' => array('S' => '{}'), ':val2' => array('S' => '-')),
    'FilterExpression' => 'lpns = :val1 and not(lastloginDate = :val2)'
));

echo "List of empty accounts\n";
foreach ($iterator as $item) {
    echo $item['registrationDate']['S'].", ".$item['lastloginDate']['S'].": ".$item['email']['S'].", ".$item['lpns']['S']."\n";

}

