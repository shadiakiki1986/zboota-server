<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../../config.php';
require_once ROOT.'/lib/connectDynamodb.php';

// get user data
$ddb=connectDynamoDb();
$iterator=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-users',
    'ProjectionExpression' => 'email,passFail,registrationDate,lastloginDate,pass',
    'ExpressionAttributeValues' =>  array ( ':val1' => array('N' => MAX_PASS_FAIL)),
    'FilterExpression' => 'passFail >= :val1'
));

echo "List of locked accounts\n";
foreach ($iterator as $item) {
    echo $item['registrationDate']['S'].", ".$item['lastloginDate']['S'].": ".$item['email']['S'].", ".$item['pass']['S']."\n";

}

